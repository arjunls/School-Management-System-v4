"use client";
import React, { useEffect, useState } from 'react';
import { examScheduleAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Select } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

export default function ExamSchedulesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [exams, setExams] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [classes, setClasses] = useState<any[]>([]);
  const [subjects, setSubjects] = useState<any[]>([]);
  const [filter, setFilter] = useState({ class_id: '', type: '' });
  const [form, setForm] = useState({ name: '', description: '', class_id: '', subject_id: '', exam_date: '', start_time: '', end_time: '', room: '', type: 'other' });
  const [editId, setEditId] = useState<number | null>(null);

  const fetch = async () => {
    try { setLoading(true); const params: any = {}; if (filter.class_id) params.class_id = filter.class_id; if (filter.type) params.type = filter.type; const res = await examScheduleAPI.getList(params); setExams(res.data?.data ?? []); }
    catch { /* */ } finally { setLoading(false); }
  };

  useEffect(() => { fetch(); classAPI.getList().then(r => setClasses(r.data?.data ?? [])).catch(() => {}); subjectAPI.getList().then(r => setSubjects(r.data?.data ?? [])).catch(() => {}); }, []);

  const openCreate = () => { setEditId(null); setForm({ name: '', description: '', class_id: '', subject_id: '', exam_date: '', start_time: '', end_time: '', room: '', type: 'other' }); setShowForm(true); };
  const openEdit = (e: any) => { setEditId(e.id); setForm({ name: e.name, description: e.description || '', class_id: String(e.class_id), subject_id: String(e.subject_id), exam_date: e.exam_date, start_time: e.start_time.slice(0, 5), end_time: e.end_time.slice(0, 5), room: e.room || '', type: e.type }); setShowForm(true); };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data = { ...form, class_id: Number(form.class_id), subject_id: Number(form.subject_id) };
      if (editId) { await examScheduleAPI.update(editId, data); toast('Updated', 'success'); }
      else { await examScheduleAPI.create(data); toast('Created', 'success'); }
      setShowForm(false); fetch();
    } catch { toast('Failed to save', 'error'); }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Delete this exam?')) return;
    try { await examScheduleAPI.delete(id); toast('Deleted', 'success'); fetch(); }
    catch { toast('Failed to delete', 'error'); }
  };

  const typeVariant = (t: string) => {
    if (t === 'midterm') return 'info' as const;
    if (t === 'final') return 'danger' as const;
    if (t === 'quiz') return 'info' as const;
    return 'default' as const;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Jadwal Ujian"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Jadwal Ujian' }]}
            action={
              user?.role !== 'student' && (
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={openCreate}
                >
                  Tambah
                </Button>
              )
            }
          />

          <div className="flex flex-wrap items-center gap-3">
            <Select
              options={[{ value: '', label: 'Semua Kelas' }, ...classes.map((c: any) => ({ value: String(c.id), label: c.name }))]}
              value={filter.class_id}
              onChange={e => setFilter(p => ({ ...p, class_id: e.target.value }))}
            />
            <Select
              options={[{ value: '', label: 'Semua Tipe' }, { value: 'midterm', label: 'Midterm' }, { value: 'final', label: 'Final' }, { value: 'quiz', label: 'Quiz' }, { value: 'other', label: 'Other' }]}
              value={filter.type}
              onChange={e => setFilter(p => ({ ...p, type: e.target.value }))}
            />
            <Button size="sm" variant="secondary" onClick={fetch}>Terapkan</Button>
          </div>

          <DataTable
            columns={[
              { key: 'name', label: 'Nama', sortable: true },
              { key: 'class', label: 'Kelas', render: (row) => row.class?.name },
              { key: 'subject', label: 'Mata Pelajaran', render: (row) => row.subject?.name },
              { key: 'exam_date', label: 'Tanggal & Waktu', render: (row) => `${new Date(row.exam_date).toLocaleDateString()} ${row.start_time?.slice(0, 5)}-${row.end_time?.slice(0, 5)}` },
              { key: 'room', label: 'Ruangan', render: (row) => row.room || '—' },
              { key: 'type', label: 'Tipe', render: (row) => <Badge variant={typeVariant(row.type)}>{row.type}</Badge> },
              { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                user?.role !== 'student' ? (
                  <div className="flex justify-end gap-1">
                    <Button variant="ghost" size="sm" onClick={() => openEdit(row)}>
                      <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    </Button>
                    <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => handleDelete(row.id)}>
                      <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    </Button>
                  </div>
                ) : null
              )},
            ]}
            data={exams}
            keyExtractor={(row) => row.id}
            loading={loading}
            emptyMessage="Belum ada jadwal ujian."
          />
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">{editId ? 'Edit Exam' : 'New Exam'}</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <input type="text" value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))} placeholder="Exam name" required className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <select value={form.class_id} onChange={e => setForm(p => ({ ...p, class_id: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="">Class</option>
                    {classes.map((c: any) => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                  <select value={form.subject_id} onChange={e => setForm(p => ({ ...p, subject_id: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="">Subject</option>
                    {subjects.map((s: any) => <option key={s.id} value={s.id}>{s.name}</option>)}
                  </select>
                </div>
                <div className="grid grid-cols-3 gap-3">
                  <input type="date" value={form.exam_date} onChange={e => setForm(p => ({ ...p, exam_date: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="time" value={form.start_time} onChange={e => setForm(p => ({ ...p, start_time: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="time" value={form.end_time} onChange={e => setForm(p => ({ ...p, end_time: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={form.room} onChange={e => setForm(p => ({ ...p, room: e.target.value }))} placeholder="Room" className="rounded-md border border-border px-3 py-2 text-sm" />
                  <select value={form.type} onChange={e => setForm(p => ({ ...p, type: e.target.value }))} className="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="other">Other</option>
                    <option value="midterm">Midterm</option>
                    <option value="final">Final</option>
                    <option value="quiz">Quiz</option>
                  </select>
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Cancel</button>
                  <Button type="submit">{editId ? 'Update' : 'Create'}</Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
