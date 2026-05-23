"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { subjectAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

interface Subject {
  id: number; name: string; code: string; description?: string;
  credits?: number; teacher_id?: number | null; teacher?: { id: number; name: string } | null;
  created_at?: string;
}

interface Pagination { total: number; per_page: number; current_page: number; last_page: number; from: number | null; to: number | null; }

function SubjectFormModal({ open, onClose, onSuccess, editing }: {
  open: boolean; onClose: () => void; onSuccess: (msg: string) => void; editing?: Subject | null;
}) {
  const [form, setForm] = useState({ name: '', code: '', description: '', credits: '1', teacher_id: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [teachers, setTeachers] = useState<{ id: number; name: string }[]>([]);

  useEffect(() => {
    if (!open) return;
    (async () => {
      try {
        const { teacherAPI } = await import('@/lib/api');
        const res = await teacherAPI.getList({ per_page: 100 });
        const body = res.data as { success?: boolean; data?: unknown[] };
        if (Array.isArray(body?.data)) setTeachers(body.data as { id: number; name: string }[]);
      } catch { /* ignore */ }
    })();
  }, [open]);

  useEffect(() => {
    if (editing) {
      setForm({
        name: editing.name, code: editing.code, description: editing.description || '',
        credits: String(editing.credits || 1), teacher_id: editing.teacher_id ? String(editing.teacher_id) : '',
      });
    } else {
      setForm({ name: '', code: '', description: '', credits: '1', teacher_id: '' });
    }
    setErrors({});
  }, [editing, open]);

  if (!open) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault(); setSaving(true); setErrors({});
    try {
      const payload = {
        name: form.name, code: form.code, description: form.description || undefined,
        credits: Number(form.credits), teacher_id: form.teacher_id ? Number(form.teacher_id) : undefined,
      };
      if (editing) {
        await subjectAPI.update(String(editing.id), payload);
        onSuccess('Subject updated');
      } else {
        await subjectAPI.create(payload);
        onSuccess('Subject created');
      }
      onClose();
    } catch (err: unknown) {
      const ae = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (ae?.response?.data?.errors) {
        const flat: Record<string, string> = {};
        for (const [f, msgs] of Object.entries(ae.response.data.errors)) flat[f] = (msgs as string[])[0];
        setErrors(flat);
      } else setErrors({ _general: ae?.response?.data?.message || 'Error saving' });
    } finally { setSaving(false); }
  };

  const ic = (f: string) => `block w-full rounded-md border-0 px-3 py-2 text-foreground shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-blue-500/50 sm:text-sm sm:leading-6 ${errors[f] ? 'ring-red-500' : 'border-input'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="rounded-xl border bg-card text-card-foreground shadow-sm-xl w-full max-w-lg mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-border flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{editing ? 'Edit Subject' : 'Add Subject'}</h2>
          <button onClick={onClose} className="text-muted-foreground/60 hover:text-foreground/70 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Subject Name *</label>
            <input required className={ic('name')} value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
            {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name}</p>}
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Code *</label>
              <input required className={ic('code')} value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} placeholder="e.g. MATH101" />
              {errors.code && <p className="mt-1 text-xs text-red-600">{errors.code}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Credits</label>
              <input type="number" min="1" max="20" className={ic('credits')} value={form.credits} onChange={(e) => setForm({ ...form, credits: e.target.value })} />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Description</label>
            <textarea rows={3} className={ic('description')} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
          </div>
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Teacher</label>
            <select className={ic('teacher_id')} value={form.teacher_id} onChange={(e) => setForm({ ...form, teacher_id: e.target.value })}>
              <option value="">No teacher</option>
              {teachers.map((t) => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} disabled={saving} className="px-4 py-2 text-sm font-medium text-foreground/80 bg-card border border-border rounded-md hover:bg-muted/50">Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-md hover:from-blue-700 hover:to-blue-600 disabled:opacity-50">{saving ? 'Saving...' : editing ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function SubjectsPage() {
  const { toast } = useToast();
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Subject | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Subject | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fetch = useCallback(async (pageNum = 1, q = '') => {
    try {
      setLoading(true);
      const params: Record<string, any> = { per_page: 10, page: pageNum };
      if (q) params.name = q;
      const res = await subjectAPI.getPaginated(params);
      const body = res.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      if (Array.isArray(body?.data)) { setSubjects(body.data as Subject[]); if (body.pagination) setPagination(body.pagination); }
      else { setSubjects([]); setPagination(null); }
    } catch { toast('Failed to load subjects', 'error'); }
    finally { setLoading(false); }
  }, [toast]);

  useEffect(() => { fetch(page, search); }, [page]);

  const handleSearch = () => { setPage(1); fetch(1, search); };

  const handleDelete = async () => {
    if (!deleteTarget) return; setDeleting(true);
    try { await subjectAPI.delete(String(deleteTarget.id)); toast('Subject deleted', 'success'); setDeleteTarget(null); fetch(page, search); }
    catch { toast('Failed to delete', 'error'); } finally { setDeleting(false); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Mata Pelajaran"
            description={pagination ? `Menampilkan ${pagination.from ?? 0}-${pagination.to ?? 0} dari ${pagination.total}` : undefined}
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Mata Pelajaran' }]}
            action={
              <div className="flex items-center gap-2">
                <Button variant="secondary" size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>}
                  onClick={() => exportAPI.download('subjects')}
                >
                  Export
                </Button>
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setEditing(null); setFormOpen(true); }}
                >
                  Tambah
                </Button>
              </div>
            }
          />

          <div className="flex flex-wrap items-center gap-3">
            <Input
              placeholder="Cari nama..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
              icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>}
            />
            <Button size="sm" onClick={handleSearch}>Cari</Button>
          </div>

          <DataTable
            columns={[
              { key: 'code', label: 'Kode', sortable: true },
              { key: 'name', label: 'Nama', sortable: true },
              { key: 'credits', label: 'SKS', sortable: true, render: (row) => row.credits != null ? String(row.credits) : '—' },
              { key: 'teacher', label: 'Guru', render: (row) => row.teacher?.name || '—' },
              { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                <div className="flex justify-end gap-1">
                  <Button variant="ghost" size="sm" onClick={() => { setEditing(row); setFormOpen(true); }}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                  </Button>
                  <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(row)}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                  </Button>
                </div>
              )},
            ]}
            data={subjects}
            keyExtractor={(row) => row.id}
            loading={loading}
            emptyMessage="Belum ada data mata pelajaran."
          />
        </div>
        <SubjectFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditing(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page, search); }} editing={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Hapus Mata Pelajaran" message={`Hapus ${deleteTarget?.name}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
