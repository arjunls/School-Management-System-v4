"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { scheduleAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

interface Schedule {
  id: number; class_id: number; subject_id: number; teacher_id?: number | null;
  day_of_week: string; start_time: string; end_time: string; room?: string | null;
  class?: { id: number; name: string } | null;
  subject?: { id: number; name: string; code: string } | null;
  teacher?: { id: number; name: string } | null;
}

interface Pagination { total: number; per_page: number; current_page: number; last_page: number; from: number | null; to: number | null; }

const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
const DAY_LABELS: Record<string, string> = { monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday', thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday' };

function ScheduleFormModal({ open, onClose, onSuccess, editing }: {
  open: boolean; onClose: () => void; onSuccess: (msg: string) => void; editing?: Schedule | null;
}) {
  const [form, setForm] = useState({ class_id: '', subject_id: '', teacher_id: '', day_of_week: 'monday', start_time: '08:00', end_time: '09:00', room: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [classes, setClasses] = useState<{ id: number; name: string }[]>([]);
  const [subjects, setSubjects] = useState<{ id: number; name: string }[]>([]);
  const [teachers, setTeachers] = useState<{ id: number; name: string }[]>([]);

  useEffect(() => {
    if (!open) return;
    (async () => {
      try {
        const api = await import('@/lib/api');
        const [cRes, sRes, tRes] = await Promise.all([
          api.classAPI.getList(),
          api.subjectAPI.getList(),
          api.teacherAPI.getList({ per_page: 100 }),
        ]);
        const ex = (d: unknown) => { const b = d as { success?: boolean; data?: unknown[] }; return Array.isArray(b?.data) ? b.data : []; };
        setClasses(ex(cRes.data) as { id: number; name: string }[]);
        setSubjects(ex(sRes.data) as { id: number; name: string }[]);
        setTeachers(ex(tRes.data) as { id: number; name: string }[]);
      } catch { /* ignore */ }
    })();
  }, [open]);

  useEffect(() => {
    if (editing) {
      setForm({
        class_id: String(editing.class_id), subject_id: String(editing.subject_id),
        teacher_id: editing.teacher_id ? String(editing.teacher_id) : '',
        day_of_week: editing.day_of_week, start_time: editing.start_time.slice(0, 5),
        end_time: editing.end_time.slice(0, 5), room: editing.room || '',
      });
    } else {
      setForm({ class_id: '', subject_id: '', teacher_id: '', day_of_week: 'monday', start_time: '08:00', end_time: '09:00', room: '' });
    }
    setErrors({});
  }, [editing, open]);

  if (!open) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault(); setSaving(true); setErrors({});
    try {
      const payload = {
        class_id: Number(form.class_id), subject_id: Number(form.subject_id),
        teacher_id: form.teacher_id ? Number(form.teacher_id) : undefined,
        day_of_week: form.day_of_week, start_time: form.start_time, end_time: form.end_time,
        room: form.room || undefined,
      };
      if (editing) {
        await scheduleAPI.update(String(editing.id), payload);
        onSuccess('Schedule updated');
      } else {
        await scheduleAPI.create(payload);
        onSuccess('Schedule created');
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
          <h2 className="text-lg font-semibold text-gray-900">{editing ? 'Edit Schedule' : 'Add Schedule'}</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Class *</label>
              <select required className={ic('class_id')} value={form.class_id} onChange={(e) => setForm({ ...form, class_id: e.target.value })}>
                <option value="">Select class...</option>
                {classes.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
              </select>
              {errors.class_id && <p className="mt-1 text-xs text-red-600">{errors.class_id}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
              <select required className={ic('subject_id')} value={form.subject_id} onChange={(e) => setForm({ ...form, subject_id: e.target.value })}>
                <option value="">Select subject...</option>
                {subjects.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
              </select>
              {errors.subject_id && <p className="mt-1 text-xs text-red-600">{errors.subject_id}</p>}
            </div>
          </div>
          <div className="grid grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Day *</label>
              <select required className={ic('day_of_week')} value={form.day_of_week} onChange={(e) => setForm({ ...form, day_of_week: e.target.value })}>
                {DAYS.map((d) => <option key={d} value={d}>{DAY_LABELS[d]}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Start *</label>
              <input type="time" required className={ic('start_time')} value={form.start_time} onChange={(e) => setForm({ ...form, start_time: e.target.value })} />
              {errors.start_time && <p className="mt-1 text-xs text-red-600">{errors.start_time}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">End *</label>
              <input type="time" required className={ic('end_time')} value={form.end_time} onChange={(e) => setForm({ ...form, end_time: e.target.value })} />
              {errors.end_time && <p className="mt-1 text-xs text-red-600">{errors.end_time}</p>}
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
              <select className={ic('teacher_id')} value={form.teacher_id} onChange={(e) => setForm({ ...form, teacher_id: e.target.value })}>
                <option value="">No teacher</option>
                {teachers.map((t) => <option key={t.id} value={t.id}>{t.name}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Room</label>
              <input className={ic('room')} value={form.room} onChange={(e) => setForm({ ...form, room: e.target.value })} placeholder="e.g. Room 101" />
              {errors.room && <p className="mt-1 text-xs text-red-600">{errors.room}</p>}
            </div>
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

export default function SchedulesPage() {
  const { toast } = useToast();
  const [schedules, setSchedules] = useState<Schedule[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Schedule | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Schedule | null>(null);
  const [deleting, setDeleting] = useState(false);
  const [dayFilter, setDayFilter] = useState('');

  const fetch = useCallback(async (pageNum = 1, day = '') => {
    try {
      setLoading(true);
      const params: Record<string, any> = { per_page: 10, page: pageNum };
      if (day) params.day_of_week = day;
      const res = await scheduleAPI.getPaginated(params);
      const body = res.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      if (Array.isArray(body?.data)) { setSchedules(body.data as Schedule[]); if (body.pagination) setPagination(body.pagination); }
      else { setSchedules([]); setPagination(null); }
    } catch { toast('Failed to load schedules', 'error'); }
    finally { setLoading(false); }
  }, [toast]);

  useEffect(() => { fetch(page, dayFilter); }, [page, dayFilter]);

  const handleDelete = async () => {
    if (!deleteTarget) return; setDeleting(true);
    try { await scheduleAPI.delete(String(deleteTarget.id)); toast('Schedule deleted', 'success'); setDeleteTarget(null); fetch(page, dayFilter); }
    catch { toast('Failed to delete', 'error'); } finally { setDeleting(false); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Schedules</h1>
              {pagination && <p className="text-sm text-gray-500 mt-1">Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}</p>}
            </div>
            <button onClick={() => { setEditing(null); setFormOpen(true); }} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Schedule</button>
          </div>
          <div className="flex items-center gap-2">
            <label className="text-sm font-medium text-gray-700">Filter by day:</label>
            <select className="rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm" value={dayFilter} onChange={(e) => { setDayFilter(e.target.value); setPage(1); }}>
              <option value="">All Days</option>
              {DAYS.map((d) => <option key={d} value={d}>{DAY_LABELS[d]}</option>)}
            </select>
          </div>
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading schedules...</div>
          ) : schedules.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No schedules found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {schedules.map((s) => (
                    <tr key={s.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{DAY_LABELS[s.day_of_week] || s.day_of_week}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{s.start_time.slice(0, 5)} – {s.end_time.slice(0, 5)}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{s.class?.name ?? `#${s.class_id}`}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{s.subject?.name ?? `#${s.subject_id}`}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{s.teacher?.name ?? '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{s.room ?? '—'}</td>
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
        <ScheduleFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditing(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page, dayFilter); }} editing={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Delete Schedule" message={`Delete schedule #${deleteTarget?.id}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
