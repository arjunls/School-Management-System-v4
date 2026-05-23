"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { gradeAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { DataTable } from '@/components/ui/DataTable';

interface Grade {
  id: number; student_id?: number; subject_id?: number;
  score?: number; grade?: string; term?: string;
  created_at?: string;
  student?: { id: number; name: string };
  subject?: { id: number; name: string; code: string };
}

interface Pagination { total: number; per_page: number; current_page: number; last_page: number; from: number | null; to: number | null; }

function GradeFormModal({ open, onClose, onSuccess, editing }: {
  open: boolean; onClose: () => void; onSuccess: (msg: string) => void; editing?: Grade | null;
}) {
  const [form, setForm] = useState({ student_id: '', subject_id: '', score: '', grade: '', term: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [students, setStudents] = useState<{ id: number; name: string }[]>([]);
  const [subjects, setSubjects] = useState<{ id: number; name: string; code: string }[]>([]);

  useEffect(() => {
    if (editing) {
      setForm({
        student_id: String(editing.student_id || ''),
        subject_id: String(editing.subject_id || ''),
        score: editing.score !== undefined && editing.score !== null ? String(editing.score) : '',
        grade: editing.grade || '',
        term: editing.term || '',
      });
    } else {
      setForm({ student_id: '', subject_id: '', score: '', grade: '', term: '' });
    }
    setErrors({});
  }, [editing, open]);

  useEffect(() => {
    if (!open) return;
    (async () => {
      try {
        const [sRes, subRes] = await Promise.all([
          import('@/lib/api').then((m) => m.studentAPI.getList({ per_page: 200 })),
          import('@/lib/api').then((m) => m.subjectAPI.getList()),
        ]);
        const sBody = sRes.data as { success?: boolean; data?: unknown[] };
        const subBody = subRes.data as { success?: boolean; data?: unknown[] };
        if (Array.isArray(sBody?.data)) setStudents(sBody.data as { id: number; name: string }[]);
        if (Array.isArray(subBody?.data)) setSubjects(subBody.data as { id: number; name: string; code: string }[]);
      } catch { /* ignore */ }
    })();
  }, [open]);

  if (!open) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault(); setSaving(true); setErrors({});
    try {
      const payload = {
        student_id: Number(form.student_id), subject_id: Number(form.subject_id),
        score: form.score ? Number(form.score) : undefined, grade: form.grade || undefined, term: form.term || undefined,
      };
      if (editing) {
        await gradeAPI.update(String(editing.id), payload);
        onSuccess('Grade updated');
      } else {
        await gradeAPI.create(payload);
        onSuccess('Grade created');
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
      <div className="rounded-xl border bg-card text-card-foreground shadow-sm-xl w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-border flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{editing ? 'Edit Grade' : 'Add Grade'}</h2>
          <button onClick={onClose} className="text-muted-foreground/60 hover:text-foreground/70 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Student *</label>
            <select required className={ic('student_id')} value={form.student_id} onChange={(e) => setForm({ ...form, student_id: e.target.value })}>
              <option value="">Select student...</option>
              {students.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
            </select>
            {errors.student_id && <p className="mt-1 text-xs text-red-600">{errors.student_id}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Subject *</label>
            <select required className={ic('subject_id')} value={form.subject_id} onChange={(e) => setForm({ ...form, subject_id: e.target.value })}>
              <option value="">Select subject...</option>
              {subjects.map((s) => <option key={s.id} value={s.id}>{s.name} ({s.code})</option>)}
            </select>
            {errors.subject_id && <p className="mt-1 text-xs text-red-600">{errors.subject_id}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Score</label>
              <input type="number" step="0.1" className={ic('score')} value={form.score} onChange={(e) => setForm({ ...form, score: e.target.value })} />
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Grade Letter</label>
              <input className="block w-full rounded-md border-0 px-3 py-2 text-foreground shadow-sm ring-1 ring-inset border-input focus:ring-2 focus:ring-inset focus:ring-blue-500/50 sm:text-sm sm:leading-6" value={form.grade} onChange={(e) => setForm({ ...form, grade: e.target.value })} />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-foreground/80 mb-1">Term</label>
            <input className="block w-full rounded-md border-0 px-3 py-2 text-foreground shadow-sm ring-1 ring-inset border-input focus:ring-2 focus:ring-inset focus:ring-blue-500/50 sm:text-sm sm:leading-6" value={form.term} onChange={(e) => setForm({ ...form, term: e.target.value })} placeholder="e.g. 2025/2026 Semester 1" />
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} disabled={saving} className="px-4 py-2 text-sm font-medium text-foreground/80 bg-card border border-border rounded-md hover:bg-muted/50">Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-md hover:from-blue-700 hover:to-blue-600 disabled:opacity-50">{saving ? 'Saving...' : editing ? 'Update' : 'Create Grade'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default function GradesPage() {
  const { toast } = useToast();
  const [grades, setGrades] = useState<Grade[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [formOpen, setFormOpen] = useState(false);
  const [editingGrade, setEditingGrade] = useState<Grade | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Grade | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fetch = useCallback(async (pageNum = 1) => {
    try {
      setLoading(true);
      const res = await gradeAPI.getPaginated({ per_page: 10, page: pageNum });
      const body = res.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      if (Array.isArray(body?.data)) { setGrades(body.data as Grade[]); if (body.pagination) setPagination(body.pagination); }
      else { setGrades([]); setPagination(null); }
    } catch { toast('Failed to load grades', 'error'); }
    finally { setLoading(false); }
  }, [toast]);

  useEffect(() => { fetch(page); }, [page]);

  const handleDelete = async () => {
    if (!deleteTarget) return; setDeleting(true);
    try { await gradeAPI.delete(String(deleteTarget.id)); toast('Grade deleted', 'success'); setDeleteTarget(null); fetch(page); }
    catch { toast('Failed to delete', 'error'); } finally { setDeleting(false); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Nilai"
            description={pagination ? `Menampilkan ${pagination.from ?? 0}-${pagination.to ?? 0} dari ${pagination.total}` : undefined}
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Nilai' }]}
            action={
              <div className="flex items-center gap-2">
                <Button variant="secondary" size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>}
                  onClick={() => exportAPI.download('grades')}
                >
                  Export
                </Button>
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setEditingGrade(null); setFormOpen(true); }}
                >
                  Tambah
                </Button>
              </div>
            }
          />

          <DataTable
            columns={[
              { key: 'id', label: 'ID', sortable: true },
              { key: 'student', label: 'Siswa', render: (row) => row.student?.name || `#${row.student_id}` },
              { key: 'subject', label: 'Mata Pelajaran', render: (row) => row.subject?.name || `#${row.subject_id}` },
              { key: 'score', label: 'Skor', sortable: true, render: (row) => row.score != null ? String(row.score) : '—' },
              { key: 'grade', label: 'Nilai', render: (row) => row.grade ? <Badge variant={Number(row.score) >= 70 ? 'success' : 'warning'}>{row.grade}</Badge> : '—' },
              { key: 'term', label: 'Semester', render: (row) => row.term || '—' },
              { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                <div className="flex justify-end gap-1">
                  <Button variant="ghost" size="sm" onClick={() => { setEditingGrade(row); setFormOpen(true); }}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                  </Button>
                  <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(row)}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                  </Button>
                </div>
              )},
            ]}
            data={grades}
            keyExtractor={(row) => row.id}
            loading={loading}
            emptyMessage="Belum ada data nilai."
          />
        </div>
        <GradeFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditingGrade(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page); }} editing={editingGrade} />
        <ConfirmDialog open={!!deleteTarget} title="Hapus Nilai" message={`Hapus nilai #${deleteTarget?.id}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
