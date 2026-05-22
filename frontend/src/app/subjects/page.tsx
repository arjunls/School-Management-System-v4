"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { subjectAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

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

  const ic = (f: string) => `block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 ${errors[f] ? 'ring-red-500' : 'ring-gray-300'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">{editing ? 'Edit Subject' : 'Add Subject'}</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label>
            <input required className={ic('name')} value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
            {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Code *</label>
              <input required className={ic('code')} value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} placeholder="e.g. MATH101" />
              {errors.code && <p className="mt-1 text-xs text-red-600">{errors.code}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Credits</label>
              <input type="number" min="1" max="20" className={ic('credits')} value={form.credits} onChange={(e) => setForm({ ...form, credits: e.target.value })} />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea rows={3} className={ic('description')} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
            <select className={ic('teacher_id')} value={form.teacher_id} onChange={(e) => setForm({ ...form, teacher_id: e.target.value })}>
              <option value="">No teacher</option>
              {teachers.map((t) => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} disabled={saving} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">{saving ? 'Saving...' : editing ? 'Update' : 'Create'}</button>
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
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Subjects</h1>
              {pagination && <p className="text-sm text-gray-500 mt-1">Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}</p>}
            </div>
            <div className="flex items-center gap-2">
              <button onClick={() => exportAPI.download('subjects')} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Export CSV</button>
              <button onClick={() => { setEditing(null); setFormOpen(true); }} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Subject</button>
            </div>
          </div>
          <div className="flex gap-2">
            <input type="text" placeholder="Search by name..." className="block rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm" value={search} onChange={(e) => setSearch(e.target.value)} onKeyDown={(e) => e.key === 'Enter' && handleSearch()} />
            <button onClick={handleSearch} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Search</button>
          </div>
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading subjects...</div>
          ) : subjects.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No subjects found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {subjects.map((s) => (
                    <tr key={s.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{s.code}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{s.name}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{s.credits ?? '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{s.teacher?.name ?? '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                        <button onClick={() => { setEditing(s); setFormOpen(true); }} className="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button onClick={() => setDeleteTarget(s)} className="text-red-600 hover:text-red-900">Delete</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
          {pagination && pagination.last_page > 1 && (
            <div className="flex items-center justify-center gap-2">
              <button onClick={() => setPage((p) => Math.max(1, p - 1))} disabled={page <= 1} className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50">Prev</button>
              {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((n) => (
                <button key={n} onClick={() => setPage(n)} className={`px-3 py-1.5 text-sm font-medium rounded-md ${n === page ? 'bg-indigo-600 text-white' : 'border border-gray-300 hover:bg-gray-50'}`}>{n}</button>
              ))}
              <button onClick={() => setPage((p) => Math.min(pagination.last_page, p + 1))} disabled={page >= pagination.last_page} className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50">Next</button>
            </div>
          )}
        </div>
        <SubjectFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditing(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page, search); }} editing={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Delete Subject" message={`Delete ${deleteTarget?.name}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
