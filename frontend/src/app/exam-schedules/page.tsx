"use client";
import React, { useEffect, useState } from 'react';
import { examScheduleAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

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

  const typeColor = (t: string) => ({ midterm: 'bg-purple-100 text-purple-800', final: 'bg-red-100 text-red-800', quiz: 'bg-blue-100 text-blue-800', other: 'bg-gray-100 text-gray-800' }[t] || '');

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Exam Schedules</h1>
            {user?.role !== 'student' && <button onClick={openCreate} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Add Exam</button>}
          </div>

          <div className="flex gap-3">
            <select value={filter.class_id} onChange={e => setFilter(p => ({ ...p, class_id: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
              <option value="">All Classes</option>
              {classes.map((c: any) => <option key={c.id} value={c.id}>{c.name}</option>)}
            </select>
            <select value={filter.type} onChange={e => setFilter(p => ({ ...p, type: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
              <option value="">All Types</option>
              <option value="midterm">Midterm</option>
              <option value="final">Final</option>
              <option value="quiz">Quiz</option>
              <option value="other">Other</option>
            </select>
            <button onClick={fetch} className="px-3 py-2 text-sm border border-gray-300 rounded-md">Apply</button>
          </div>

          {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
            exams.length === 0 ? <div className="text-center py-12 text-gray-500">No exams scheduled</div> :
            <div className="bg-white rounded-lg shadow border overflow-hidden">
              <table className="min-w-full divide-y divide-gray-200 text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Class</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Subject</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Date & Time</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Room</th>
                    <th className="px-4 py-3 text-center font-medium text-gray-500">Type</th>
                    <th className="px-4 py-3 text-right font-medium text-gray-500">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {exams.map(e => (
                    <tr key={e.id}>
                      <td className="px-4 py-3 font-medium">{e.name}</td>
                      <td className="px-4 py-3">{e.class?.name}</td>
                      <td className="px-4 py-3">{e.subject?.name}</td>
                      <td className="px-4 py-3">{new Date(e.exam_date).toLocaleDateString()} {e.start_time?.slice(0, 5)}-{e.end_time?.slice(0, 5)}</td>
                      <td className="px-4 py-3">{e.room || '—'}</td>
                      <td className="px-4 py-3 text-center"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${typeColor(e.type)}`}>{e.type}</span></td>
                      <td className="px-4 py-3 text-right">
                        {user?.role !== 'student' && <><button onClick={() => openEdit(e)} className="text-indigo-600 hover:text-indigo-800 mr-2">Edit</button><button onClick={() => handleDelete(e.id)} className="text-red-600 hover:text-red-800">Delete</button></>}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          }
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">{editId ? 'Edit Exam' : 'New Exam'}</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <input type="text" value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))} placeholder="Exam name" required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <select value={form.class_id} onChange={e => setForm(p => ({ ...p, class_id: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Class</option>
                    {classes.map((c: any) => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                  <select value={form.subject_id} onChange={e => setForm(p => ({ ...p, subject_id: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Subject</option>
                    {subjects.map((s: any) => <option key={s.id} value={s.id}>{s.name}</option>)}
                  </select>
                </div>
                <div className="grid grid-cols-3 gap-3">
                  <input type="date" value={form.exam_date} onChange={e => setForm(p => ({ ...p, exam_date: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="time" value={form.start_time} onChange={e => setForm(p => ({ ...p, start_time: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="time" value={form.end_time} onChange={e => setForm(p => ({ ...p, end_time: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={form.room} onChange={e => setForm(p => ({ ...p, room: e.target.value }))} placeholder="Room" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <select value={form.type} onChange={e => setForm(p => ({ ...p, type: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="other">Other</option>
                    <option value="midterm">Midterm</option>
                    <option value="final">Final</option>
                    <option value="quiz">Quiz</option>
                  </select>
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">{editId ? 'Update' : 'Create'}</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
