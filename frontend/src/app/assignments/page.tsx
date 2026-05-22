"use client";
import React, { useEffect, useState } from 'react';
import { assignmentAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function AssignmentsPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [assignments, setAssignments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [selected, setSelected] = useState<any>(null);
  const [form, setForm] = useState({ title: '', description: '', class_id: '', subject_id: '', due_date: '', max_score: 100 });
  const [classes, setClasses] = useState<any[]>([]);
  const [subjects, setSubjects] = useState<any[]>([]);
  const [submitNotes, setSubmitNotes] = useState('');
  const [gradeForm, setGradeForm] = useState({ score: '', feedback: '' });

  const fetch = async () => {
    try { setLoading(true); const res = await assignmentAPI.getList(); setAssignments(res.data?.data ?? []); }
    catch { /* */ } finally { setLoading(false); }
  };

  useEffect(() => { fetch(); classAPI.getList().then(r => setClasses(r.data?.data ?? [])).catch(() => {}); subjectAPI.getList().then(r => setSubjects(r.data?.data ?? [])).catch(() => {}); }, []);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await assignmentAPI.create({ ...form, class_id: Number(form.class_id), subject_id: Number(form.subject_id), max_score: Number(form.max_score) });
      toast('Assignment created', 'success'); setShowForm(false); setForm({ title: '', description: '', class_id: '', subject_id: '', due_date: '', max_score: 100 }); fetch();
    } catch { toast('Failed to create', 'error'); }
  };

  const handleSubmit = async (assignmentId: number) => {
    try { await assignmentAPI.submit(assignmentId, { notes: submitNotes }); toast('Submitted', 'success'); setSelected(null); fetch(); }
    catch { toast('Failed to submit', 'error'); }
  };

  const handleGrade = async (assignmentId: number, subId: number) => {
    try { await assignmentAPI.grade(assignmentId, subId, { score: Number(gradeForm.score), feedback: gradeForm.feedback }); toast('Graded', 'success'); setSelected(null); fetch(); }
    catch { toast('Failed to grade', 'error'); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Assignments</h1>
            {user?.role !== 'student' && <button onClick={() => setShowForm(true)} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ New Assignment</button>}
          </div>

          {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
            assignments.length === 0 ? <div className="text-center py-12 text-gray-500">No assignments</div> :
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              {assignments.map(a => (
                <div key={a.id} className="bg-white rounded-lg shadow border p-4">
                  <div className="flex items-start justify-between mb-2">
                    <h3 className="font-semibold text-gray-900">{a.title}</h3>
                    <span className={`text-xs px-2 py-1 rounded-full ${new Date(a.due_date) > new Date() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {new Date(a.due_date) > new Date() ? 'Open' : 'Closed'}
                    </span>
                  </div>
                  <p className="text-xs text-gray-500 mb-2">{a.subject?.name} — {a.class?.name}</p>
                  <p className="text-sm text-gray-600 mb-3 line-clamp-2">{a.description || 'No description'}</p>
                  <div className="flex items-center justify-between text-xs text-gray-400">
                    <span>Due: {new Date(a.due_date).toLocaleDateString()}</span>
                    <span>Max: {a.max_score}</span>
                  </div>
                  <button onClick={() => { setSelected(a); setGradeForm({ score: '', feedback: '' }); setSubmitNotes(''); }} className="mt-3 w-full text-sm text-indigo-600 hover:text-indigo-800 font-medium">View Details</button>
                </div>
              ))}
            </div>
          }
        </div>

        {/* Create Form Modal */}
        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">New Assignment</h2>
              <form onSubmit={handleCreate} className="space-y-4">
                <input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Title" required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={3} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
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
                <div className="grid grid-cols-2 gap-3">
                  <input type="datetime-local" value={form.due_date} onChange={e => setForm(p => ({ ...p, due_date: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="number" value={form.max_score} onChange={e => setForm(p => ({ ...p, max_score: Number(e.target.value) }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Create</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Detail Modal */}
        {selected && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-2xl bg-white rounded-lg shadow-lg p-6 max-h-[80vh] overflow-y-auto">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-semibold">{selected.title}</h2>
                <button onClick={() => setSelected(null)} className="text-gray-400 hover:text-gray-600">&times;</button>
              </div>
              <p className="text-sm text-gray-500 mb-2">{selected.subject?.name} — {selected.class?.name}</p>
              <p className="text-sm text-gray-700 mb-4">{selected.description || 'No description'}</p>
              <p className="text-xs text-gray-400 mb-4">Due: {new Date(selected.due_date).toLocaleString()} | Max score: {selected.max_score}</p>

              {user?.role === 'student' ? (
                <div className="border-t pt-4">
                  <h3 className="font-medium mb-2">Submit</h3>
                  <textarea value={submitNotes} onChange={e => setSubmitNotes(e.target.value)} placeholder="Notes (optional)" rows={3} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm mb-3" />
                  <button onClick={() => handleSubmit(selected.id)} className="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">Submit</button>
                </div>
              ) : (
                <div className="border-t pt-4">
                  <h3 className="font-medium mb-2">Submissions ({selected.submissions?.length || 0})</h3>
                  {selected.submissions?.map((sub: any) => (
                    <div key={sub.id} className="border rounded-md p-3 mb-2">
                      <p className="text-sm font-medium">{sub.student?.name}</p>
                      <p className="text-xs text-gray-500">Submitted: {new Date(sub.submitted_at).toLocaleString()}</p>
                      {sub.notes && <p className="text-sm text-gray-600 mt-1">{sub.notes}</p>}
                      {sub.score !== null ? (
                        <p className="text-sm mt-1">Score: <span className="font-semibold">{sub.score}/{selected.max_score}</span></p>
                      ) : (
                        <div className="mt-2 flex gap-2">
                          <input type="number" placeholder="Score" value={gradeForm.score} onChange={e => setGradeForm(p => ({ ...p, score: e.target.value }))} className="w-20 rounded-md border border-gray-300 px-2 py-1 text-sm" />
                          <input type="text" placeholder="Feedback" value={gradeForm.feedback} onChange={e => setGradeForm(p => ({ ...p, feedback: e.target.value }))} className="flex-1 rounded-md border border-gray-300 px-2 py-1 text-sm" />
                          <button onClick={() => handleGrade(selected.id, sub.id)} className="px-3 py-1 bg-green-600 text-white rounded-md text-xs">Grade</button>
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
