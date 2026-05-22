"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { gradeAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

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

  const ic = (f: string) => `block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 ${errors[f] ? 'ring-red-500' : 'ring-gray-300'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">{editing ? 'Edit Grade' : 'Add Grade'}</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Student *</label>
            <select required className={ic('student_id')} value={form.student_id} onChange={(e) => setForm({ ...form, student_id: e.target.value })}>
              <option value="">Select student...</option>
              {students.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
            </select>
            {errors.student_id && <p className="mt-1 text-xs text-red-600">{errors.student_id}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
            <select required className={ic('subject_id')} value={form.subject_id} onChange={(e) => setForm({ ...form, subject_id: e.target.value })}>
              <option value="">Select subject...</option>
              {subjects.map((s) => <option key={s.id} value={s.id}>{s.name} ({s.code})</option>)}
            </select>
            {errors.subject_id && <p className="mt-1 text-xs text-red-600">{errors.subject_id}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Score</label>
              <input type="number" step="0.1" className={ic('score')} value={form.score} onChange={(e) => setForm({ ...form, score: e.target.value })} />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Grade Letter</label>
              <input className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.grade} onChange={(e) => setForm({ ...form, grade: e.target.value })} />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Term</label>
            <input className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.term} onChange={(e) => setForm({ ...form, term: e.target.value })} placeholder="e.g. 2025/2026 Semester 1" />
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} disabled={saving} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">{saving ? 'Saving...' : editing ? 'Update' : 'Create Grade'}</button>
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
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Grades</h1>
              {pagination && <p className="text-sm text-gray-500 mt-1">Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}</p>}
            </div>
            <div className="flex items-center gap-2">
              <button onClick={() => exportAPI.download('grades')} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Export CSV</button>
              <button onClick={() => { setEditingGrade(null); setFormOpen(true); }} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Grade</button>
            </div>
          </div>
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading grades...</div>
          ) : grades.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No grades found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {grades.map((g) => (
                    <tr key={g.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{g.id}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{g.student?.name || `#${g.student_id}`}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{g.subject?.name || `#${g.subject_id}`}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{g.score ?? '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">{g.grade || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{g.term || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                        <button onClick={() => { setEditingGrade(g); setFormOpen(true); }} className="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button onClick={() => setDeleteTarget(g)} className="text-red-600 hover:text-red-900">Delete</button>
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
        <GradeFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditingGrade(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page); }} editing={editingGrade} />
        <ConfirmDialog open={!!deleteTarget} title="Delete Grade" message={`Delete grade #${deleteTarget?.id}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
