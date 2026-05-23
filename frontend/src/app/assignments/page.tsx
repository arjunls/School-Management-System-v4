"use client";
import React, { useEffect, useState } from 'react';
import { assignmentAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input, Select } from '@/components/ui/Input';

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

  const dueVariant = (due: string) => new Date(due) > new Date() ? 'success' as const : 'danger' as const;

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Tugas"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Tugas' }]}
            action={
              user?.role !== 'student' && (
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => setShowForm(true)}
                >
                  Buat
                </Button>
              )
            }
          />

          {loading ? <div className="text-center py-12 text-muted-foreground">Memuat...</div> :
            assignments.length === 0 ? <div className="text-center py-12 text-muted-foreground">Belum ada tugas</div> :
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              {assignments.map(a => (
                <div key={a.id} className="rounded-xl border bg-card text-card-foreground shadow-sm p-4">
                  <div className="flex items-start justify-between mb-2">
                    <h3 className="font-semibold text-foreground">{a.title}</h3>
                    <Badge variant={dueVariant(a.due_date)}>{new Date(a.due_date) > new Date() ? 'Buka' : 'Tutup'}</Badge>
                  </div>
                  <p className="text-xs text-muted-foreground mb-2">{a.subject?.name} — {a.class?.name}</p>
                  <p className="text-sm text-foreground/70 mb-3 line-clamp-2">{a.description || 'Tidak ada deskripsi'}</p>
                  <div className="flex items-center justify-between text-xs text-muted-foreground/60">
                    <span>Tenggat: {new Date(a.due_date).toLocaleDateString()}</span>
                    <span>Maks: {a.max_score}</span>
                  </div>
                  <Button variant="ghost" size="sm" onClick={() => { setSelected(a); setGradeForm({ score: '', feedback: '' }); setSubmitNotes(''); }} className="mt-3 w-full">Lihat Detail</Button>
                </div>
              ))}
            </div>
          }
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Tugas Baru</h2>
              <form onSubmit={handleCreate} className="space-y-4">
                <Input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Judul" required />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Deskripsi" rows={3} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <Select value={form.class_id} onChange={e => setForm(p => ({ ...p, class_id: e.target.value }))} options={[{ value: '', label: 'Kelas' }, ...classes.map((c: any) => ({ value: String(c.id), label: c.name }))]} />
                  <Select value={form.subject_id} onChange={e => setForm(p => ({ ...p, subject_id: e.target.value }))} options={[{ value: '', label: 'Mata Pelajaran' }, ...subjects.map((s: any) => ({ value: String(s.id), label: s.name }))]} />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <Input type="datetime-local" value={form.due_date} onChange={e => setForm(p => ({ ...p, due_date: e.target.value }))} required />
                  <Input type="number" value={form.max_score} onChange={e => setForm(p => ({ ...p, max_score: Number(e.target.value) }))} label="Skor Maks" />
                </div>
                <div className="flex justify-end gap-3">
                  <Button variant="outline" type="button" onClick={() => setShowForm(false)}>Batal</Button>
                  <Button type="submit">Buat</Button>
                </div>
              </form>
            </div>
          </div>
        )}

        {selected && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-2xl rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6 max-h-[80vh] overflow-y-auto">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-semibold">{selected.title}</h2>
                <button onClick={() => setSelected(null)} className="text-muted-foreground/60 hover:text-foreground/70">&times;</button>
              </div>
              <p className="text-sm text-muted-foreground mb-2">{selected.subject?.name} — {selected.class?.name}</p>
              <p className="text-sm text-foreground/80 mb-4">{selected.description || 'Tidak ada deskripsi'}</p>
              <p className="text-xs text-muted-foreground/60 mb-4">Tenggat: {new Date(selected.due_date).toLocaleString()} | Skor maks: {selected.max_score}</p>

              {user?.role === 'student' ? (
                <div className="border-t pt-4">
                  <h3 className="font-medium mb-2">Kumpulkan</h3>
                  <textarea value={submitNotes} onChange={e => setSubmitNotes(e.target.value)} placeholder="Catatan (opsional)" rows={3} className="block w-full rounded-md border border-border px-3 py-2 text-sm mb-3" />
                  <Button onClick={() => handleSubmit(selected.id)}>Kumpulkan</Button>
                </div>
              ) : (
                <div className="border-t pt-4">
                  <h3 className="font-medium mb-2">Pengumpulan ({selected.submissions?.length || 0})</h3>
                  {selected.submissions?.map((sub: any) => (
                    <div key={sub.id} className="border rounded-md p-3 mb-2">
                      <p className="text-sm font-medium">{sub.student?.name}</p>
                      <p className="text-xs text-muted-foreground">Dikumpulkan: {new Date(sub.submitted_at).toLocaleString()}</p>
                      {sub.notes && <p className="text-sm text-foreground/70 mt-1">{sub.notes}</p>}
                      {sub.score !== null ? (
                        <p className="text-sm mt-1">Skor: <span className="font-semibold">{sub.score}/{selected.max_score}</span></p>
                      ) : (
                        <div className="mt-2 flex flex-wrap gap-2">
                          <Input type="number" placeholder="Skor" value={gradeForm.score} onChange={e => setGradeForm(p => ({ ...p, score: e.target.value }))} className="w-20" />
                          <Input type="text" placeholder="Umpan balik" value={gradeForm.feedback} onChange={e => setGradeForm(p => ({ ...p, feedback: e.target.value }))} className="min-w-[120px] flex-1" />
                          <Button size="sm" onClick={() => handleGrade(selected.id, sub.id)}>Nilai</Button>
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
