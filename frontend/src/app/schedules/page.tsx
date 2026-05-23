"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { scheduleAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Select } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

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

  const ic = (f: string) => `block w-full rounded-md border-0 px-3 py-2 text-foreground shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-blue-500/50 sm:text-sm sm:leading-6 ${errors[f] ? 'ring-red-500' : 'border-input'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="rounded-xl border bg-card text-card-foreground shadow-sm-xl w-full max-w-lg mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-border flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{editing ? 'Edit Schedule' : 'Add Schedule'}</h2>
          <button onClick={onClose} className="text-muted-foreground/60 hover:text-foreground/70 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Class *</label>
              <select required className={ic('class_id')} value={form.class_id} onChange={(e) => setForm({ ...form, class_id: e.target.value })}>
                <option value="">Select class...</option>
                {classes.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
              </select>
              {errors.class_id && <p className="mt-1 text-xs text-red-600">{errors.class_id}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Subject *</label>
              <select required className={ic('subject_id')} value={form.subject_id} onChange={(e) => setForm({ ...form, subject_id: e.target.value })}>
                <option value="">Select subject...</option>
                {subjects.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
              </select>
              {errors.subject_id && <p className="mt-1 text-xs text-red-600">{errors.subject_id}</p>}
            </div>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Day *</label>
              <select required className={ic('day_of_week')} value={form.day_of_week} onChange={(e) => setForm({ ...form, day_of_week: e.target.value })}>
                {DAYS.map((d) => <option key={d} value={d}>{DAY_LABELS[d]}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Start *</label>
              <input type="time" required className={ic('start_time')} value={form.start_time} onChange={(e) => setForm({ ...form, start_time: e.target.value })} />
              {errors.start_time && <p className="mt-1 text-xs text-red-600">{errors.start_time}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">End *</label>
              <input type="time" required className={ic('end_time')} value={form.end_time} onChange={(e) => setForm({ ...form, end_time: e.target.value })} />
              {errors.end_time && <p className="mt-1 text-xs text-red-600">{errors.end_time}</p>}
            </div>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Teacher</label>
              <select className={ic('teacher_id')} value={form.teacher_id} onChange={(e) => setForm({ ...form, teacher_id: e.target.value })}>
                <option value="">No teacher</option>
                {teachers.map((t) => <option key={t.id} value={t.id}>{t.name}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-foreground/80 mb-1">Room</label>
              <input className={ic('room')} value={form.room} onChange={(e) => setForm({ ...form, room: e.target.value })} placeholder="e.g. Room 101" />
              {errors.room && <p className="mt-1 text-xs text-red-600">{errors.room}</p>}
            </div>
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
          <PageHeader
            title="Jadwal"
            description={pagination ? `Menampilkan ${pagination.from ?? 0}-${pagination.to ?? 0} dari ${pagination.total}` : undefined}
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Jadwal' }]}
            action={
              <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                onClick={() => { setEditing(null); setFormOpen(true); }}
              >
                Tambah
              </Button>
            }
          />

          <div className="flex items-center gap-3">
            <Select
              label="Filter hari"
              options={[{ value: '', label: 'Semua Hari' }, ...DAYS.map(d => ({ value: d, label: DAY_LABELS[d] }))]}
              value={dayFilter}
              onChange={(e) => { setDayFilter(e.target.value); setPage(1); }}
            />
          </div>

          <DataTable
            columns={[
              { key: 'day_of_week', label: 'Hari', sortable: true, render: (row) => DAY_LABELS[row.day_of_week] || row.day_of_week },
              { key: 'start_time', label: 'Waktu', render: (row) => `${row.start_time.slice(0, 5)} – ${row.end_time.slice(0, 5)}` },
              { key: 'class', label: 'Kelas', render: (row) => row.class?.name || `#${row.class_id}` },
              { key: 'subject', label: 'Mata Pelajaran', render: (row) => row.subject?.name || `#${row.subject_id}` },
              { key: 'teacher', label: 'Guru', render: (row) => row.teacher?.name || '—' },
              { key: 'room', label: 'Ruangan', render: (row) => row.room || '—' },
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
            data={schedules}
            keyExtractor={(row) => row.id}
            loading={loading}
            emptyMessage="Belum ada data jadwal."
          />
        </div>
        <ScheduleFormModal open={formOpen} onClose={() => { setFormOpen(false); setEditing(null); }} onSuccess={(msg) => { toast(msg, 'success'); fetch(page, dayFilter); }} editing={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Hapus Jadwal" message={`Hapus jadwal #${deleteTarget?.id}?`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
