"use client";
import React, { useEffect, useState } from 'react';
import { quizAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input, Select } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

export default function QuizzesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [tab, setTab] = useState<'quizzes' | 'attempts' | 'take'>('quizzes');
  const [quizzes, setQuizzes] = useState<any[]>([]);
  const [attempts, setAttempts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [classes, setClasses] = useState<any[]>([]);
  const [subjects, setSubjects] = useState<any[]>([]);

  const [showForm, setShowForm] = useState(false);
  const [editQuiz, setEditQuiz] = useState<any>(null);
  const [form, setForm] = useState({ title: '', description: '', class_id: '', subject_id: '', time_limit: '', passing_score: '0', due_date: '', status: 'draft' });

  const [showQuestions, setShowQuestions] = useState<any>(null);
  const [questions, setQuestions] = useState<any[]>([]);
  const [qForm, setQForm] = useState({ question_text: '', type: 'multiple_choice', options: [] as any[], correct_answer: '', points: '1' });
  const [qOption, setQOption] = useState('');

  const [takingQuiz, setTakingQuiz] = useState<any>(null);
  const [attempt, setAttempt] = useState<any>(null);
  const [answers, setAnswers] = useState<Record<number, string>>({});
  const [timeLeft, setTimeLeft] = useState(0);

  const [showGrading, setShowGrading] = useState<any>(null);
  const [gradeScores, setGradeScores] = useState<Record<number, string>>({});

  const fetchQuizzes = async () => {
    try { setLoading(true); const res = await quizAPI.getAll(); setQuizzes(res.data?.data?.data ?? res.data?.data ?? []); } finally { setLoading(false); }
  };
  const fetchAttempts = async () => {
    try { setLoading(true); const res = await quizAPI.attempts(); setAttempts(res.data?.data?.data ?? res.data?.data ?? []); } finally { setLoading(false); }
  };

  useEffect(() => {
    if (tab === 'quizzes') fetchQuizzes();
    else if (tab === 'attempts') fetchAttempts();
  }, [tab]);

  useEffect(() => {
    classAPI.getList({ per_page: 50 }).then(r => setClasses(r.data?.data ?? [])).catch(() => {});
    subjectAPI.getList({ per_page: 50 }).then(r => setSubjects(r.data?.data ?? [])).catch(() => {});
  }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { ...form, time_limit: form.time_limit ? Number(form.time_limit) : null, passing_score: Number(form.passing_score), class_id: Number(form.class_id), subject_id: Number(form.subject_id) };
      if (editQuiz) { await quizAPI.update(editQuiz.id, data); toast('Updated', 'success'); }
      else { await quizAPI.create(data); toast('Created', 'success'); }
      setShowForm(false); setEditQuiz(null); setForm({ title: '', description: '', class_id: '', subject_id: '', time_limit: '', passing_score: '0', due_date: '', status: 'draft' }); fetchQuizzes();
    } catch { toast('Failed', 'error'); }
  };

  const handleAddQuestion = async () => {
    try {
      const data: any = { question_text: qForm.question_text, type: qForm.type, points: Number(qForm.points) };
      if (qForm.type === 'multiple_choice') { data.options = qForm.options; data.correct_answer = qForm.correct_answer; }
      await quizAPI.addQuestion(showQuestions.id, data);
      toast('Question added', 'success');
      loadQuestions(showQuestions.id);
      setQForm({ question_text: '', type: 'multiple_choice', options: [], correct_answer: '', points: '1' });
    } catch { toast('Failed', 'error'); }
  };

  const handleDeleteQuestion = async (id: number) => {
    try { await quizAPI.deleteQuestion(id); toast('Deleted', 'success'); loadQuestions(showQuestions.id); } catch { toast('Failed', 'error'); }
  };

  const loadQuestions = async (id: number) => {
    try { const res = await quizAPI.get(id); const q = res.data?.data; setShowQuestions(q); setQuestions(q?.questions ?? []); } catch {}
  };

  const handleStartQuiz = async (quizId: number) => {
    try {
      const res = await quizAPI.start(quizId);
      const d = res.data?.data;
      setAttempt(d.attempt); setTakingQuiz(d.quiz);
      setAnswers({});
      if (d.quiz.time_limit) setTimeLeft(d.quiz.time_limit * 60);
      setTab('take');
    } catch { toast('Failed to start', 'error'); }
  };

  const handleSubmitQuiz = async () => {
    try {
      const ans = Object.entries(answers).map(([question_id, answer_text]) => ({ question_id: Number(question_id), answer_text }));
      await quizAPI.submit(attempt.id, { answers: ans });
      toast('Submitted!', 'success');
      setTakingQuiz(null); setAttempt(null); setTab('quizzes');
    } catch { toast('Failed', 'error'); }
  };

  const handleEssayGrade = async (attemptId: number, questionId: number) => {
    try { await quizAPI.gradeEssay(attemptId, questionId, { score: Number(gradeScores[questionId]) }); toast('Graded', 'success'); } catch { toast('Failed', 'error'); }
  };

  useEffect(() => {
    if (timeLeft <= 0 || tab !== 'take') return;
    const t = setInterval(() => setTimeLeft(p => { if (p <= 1) { handleSubmitQuiz(); return 0; } return p - 1; }), 1000);
    return () => clearInterval(t);
  }, [timeLeft, tab]);

  const fmtTime = (s: number) => `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;

  const statusVariant = (s: string) => {
    if (s === 'published') return 'success' as const;
    if (s === 'draft') return 'default' as const;
    return 'default' as const;
  };

  const attemptStatusVariant = (s: string) => {
    if (s === 'graded') return 'success' as const;
    if (s === 'submitted') return 'warning' as const;
    return 'info' as const;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Kuis"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Kuis' }]}
            action={
              <div className="flex gap-2">
                <button onClick={() => setTab('quizzes')} className={`px-3 py-2 text-sm rounded-md ${tab === 'quizzes' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Kuis</button>
                <button onClick={() => setTab('attempts')} className={`px-3 py-2 text-sm rounded-md ${tab === 'attempts' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Percobaan</button>
              </div>
            }
          />

          {tab === 'take' && takingQuiz ? (
            <div>
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-semibold">{takingQuiz.title}</h2>
                {timeLeft > 0 && <span className="text-lg font-mono text-red-600">{fmtTime(timeLeft)}</span>}
              </div>
              <div className="space-y-4">
                {takingQuiz.questions?.map((q: any) => (
                  <div key={q.id} className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
                    <p className="font-medium mb-2">{q.question_text} <span className="text-sm text-muted-foreground/60">({q.points}pt)</span></p>
                    {q.type === 'multiple_choice' ? (
                      <div className="space-y-2">
                        {(q.options ?? []).map((opt: any, i: number) => (
                          <label key={i} className="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name={`q_${q.id}`} value={opt.key} checked={answers[q.id] === opt.key} onChange={e => setAnswers(p => ({ ...p, [q.id]: e.target.value }))} className="accent-indigo-600" />
                            {opt.value}
                          </label>
                        ))}
                      </div>
                    ) : (
                      <textarea value={answers[q.id] ?? ''} onChange={e => setAnswers(p => ({ ...p, [q.id]: e.target.value }))} rows={3} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    )}
                  </div>
                ))}
                <Button onClick={handleSubmitQuiz}>Kumpulkan</Button>
              </div>
            </div>
          ) : tab === 'quizzes' ? (
            <>
              {user?.role !== 'student' && <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>} onClick={() => { setEditQuiz(null); setForm({ title: '', description: '', class_id: '', subject_id: '', time_limit: '', passing_score: '0', due_date: '', status: 'draft' }); setShowForm(true); }}>Buat Kuis</Button>}
              {loading ? <div className="text-center py-12 text-muted-foreground">Memuat...</div> :
                quizzes.length === 0 ? <div className="text-center py-12 text-muted-foreground">Belum ada kuis</div> :
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                  {quizzes.map(q => (
                    <div key={q.id} className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
                      <div className="flex items-center justify-between mb-1">
                        <h3 className="font-semibold">{q.title}</h3>
                        <Badge variant={statusVariant(q.status)}>{q.status}</Badge>
                      </div>
                      <p className="text-xs text-muted-foreground">{q.class?.name} — {q.subject?.name}</p>
                      {q.time_limit && <p className="text-xs text-muted-foreground/60 mt-1">Waktu: {q.time_limit}menit</p>}
                      <div className="mt-3 flex gap-2 justify-end">
                        {user?.role === 'student' ? (
                          q.status === 'published' && <Button size="sm" onClick={() => handleStartQuiz(q.id)}>Mulai</Button>
                        ) : (
                          <>
                            <Button variant="outline" size="sm" onClick={() => loadQuestions(q.id)}>Soal</Button>
                            <Button variant="outline" size="sm" onClick={() => { setEditQuiz(q); setForm({ title: q.title, description: q.description ?? '', class_id: String(q.class_id), subject_id: String(q.subject_id), time_limit: String(q.time_limit ?? ''), passing_score: String(q.passing_score ?? '0'), due_date: q.due_date ? q.due_date.slice(0,16) : '', status: q.status }); setShowForm(true); }}>Edit</Button>
                          </>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              }
            </>
          ) : (
            <>
              {loading ? <div className="text-center py-12 text-muted-foreground">Memuat...</div> :
                attempts.length === 0 ? <div className="text-center py-12 text-muted-foreground">Belum ada percobaan</div> :
                <DataTable
                  columns={[
                    { key: 'quiz', label: 'Kuis', render: (row: any) => row.quiz?.title },
                    ...(user?.role !== 'student' ? [{ key: 'student', label: 'Siswa', render: (row: any) => row.student?.name }] : []),
                    { key: 'score', label: 'Skor', render: (row: any) => row.score ?? '—' },
                    { key: 'status', label: 'Status', render: (row: any) => <Badge variant={attemptStatusVariant(row.status)}>{row.status}</Badge> },
                    { key: 'id', label: 'Aksi', className: 'text-right', render: (row: any) => (
                      user?.role !== 'student' && row.status === 'submitted' ? (
                        <Button variant="ghost" size="sm" onClick={async () => { try { const initScores: Record<number, string> = {}; setShowGrading(row); setGradeScores(initScores); } catch {} }}>
                          <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                          Nilai
                        </Button>
                      ) : null
                    )},
                  ]}
                  data={attempts}
                  keyExtractor={(row: any) => row.id}
                  emptyMessage="Belum ada percobaan."
                />
              }
            </>
          )}
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-card dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">{editQuiz ? 'Edit' : 'Buat'} Kuis</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <Input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Judul" required />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Deskripsi" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <Select value={form.class_id} onChange={e => setForm(p => ({ ...p, class_id: e.target.value }))} options={[{ value: '', label: 'Kelas' }, ...classes.map((c: any) => ({ value: String(c.id), label: c.name }))]} />
                  <Select value={form.subject_id} onChange={e => setForm(p => ({ ...p, subject_id: e.target.value }))} options={[{ value: '', label: 'Mata Pelajaran' }, ...subjects.map((s: any) => ({ value: String(s.id), label: s.name }))]} />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <Input type="number" value={form.time_limit} onChange={e => setForm(p => ({ ...p, time_limit: e.target.value }))} placeholder="Batas waktu (menit)" />
                  <Input type="number" value={form.passing_score} onChange={e => setForm(p => ({ ...p, passing_score: e.target.value }))} placeholder="Skor kelulusan" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <Input type="datetime-local" value={form.due_date} onChange={e => setForm(p => ({ ...p, due_date: e.target.value }))} />
                  <Select value={form.status} onChange={e => setForm(p => ({ ...p, status: e.target.value }))} options={[{ value: 'draft', label: 'Draft' }, { value: 'published', label: 'Terbit' }]} />
                </div>
                <div className="flex justify-end gap-3">
                  <Button variant="outline" type="button" onClick={() => setShowForm(false)}>Batal</Button>
                  <Button type="submit">{editQuiz ? 'Update' : 'Buat'}</Button>
                </div>
              </form>
            </div>
          </div>
        )}

        {showQuestions && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-2xl bg-card dark:bg-slate-800 rounded-lg shadow-lg p-6 max-h-[80vh] overflow-y-auto">
              <h2 className="text-lg font-semibold mb-4">{showQuestions.title} — Soal</h2>
              <div className="space-y-3 mb-4">
                {questions.map(q => (
                  <div key={q.id} className="border dark:border-slate-600 rounded-lg p-3">
                    <div className="flex justify-between items-start">
                      <div className="flex-1 text-sm">
                        <p>{q.question_text} <span className="text-muted-foreground/60">({q.points}pt, {q.type})</span></p>
                        {q.type === 'multiple_choice' && q.options && <div className="mt-1 text-xs text-muted-foreground">{q.options.map((o: any, i: number) => <span key={i} className={`mr-2 ${o.key === q.correct_answer ? 'text-green-600 font-medium' : ''}`}>{o.key}: {o.value}</span>)}</div>}
                      </div>
                      <Button variant="ghost" size="sm" className="text-destructive" onClick={() => handleDeleteQuestion(q.id)}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
              <div className="border-t dark:border-slate-700 pt-4">
                <h3 className="text-sm font-semibold mb-2">Tambah Soal</h3>
                <div className="space-y-3">
                  <textarea value={qForm.question_text} onChange={e => setQForm(p => ({ ...p, question_text: e.target.value }))} placeholder="Teks soal" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <div className="grid grid-cols-2 gap-3">
                    <Select value={qForm.type} onChange={e => setQForm(p => ({ ...p, type: e.target.value, options: [], correct_answer: '' }))} options={[{ value: 'multiple_choice', label: 'Pilihan Ganda' }, { value: 'essay', label: 'Esai' }]} />
                    <Input type="number" value={qForm.points} onChange={e => setQForm(p => ({ ...p, points: e.target.value }))} placeholder="Poin" min={1} />
                  </div>
                  {qForm.type === 'multiple_choice' && (
                    <div className="space-y-2">
                      {qForm.options.map((o, i) => (
                        <div key={i} className="flex items-center gap-2">
                          <span className="text-xs font-medium">{o.key}</span>
                          <input type="text" value={o.value} onChange={e => { const opts = [...qForm.options]; opts[i].value = e.target.value; setQForm(p => ({ ...p, options: opts })); }} className="flex-1 rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" />
                          <input type="radio" name="correct" checked={qForm.correct_answer === o.key} onChange={() => setQForm(p => ({ ...p, correct_answer: o.key }))} className="accent-indigo-600" />
                        </div>
                      ))}
                      <Button type="button" variant="ghost" size="sm" onClick={() => setQForm(p => ({ ...p, options: [...p.options, { key: String.fromCharCode(65 + p.options.length), value: '' }] }))}>+ Tambah opsi</Button>
                    </div>
                  )}
                  <Button onClick={handleAddQuestion}>Tambah</Button>
                </div>
              </div>
              <div className="flex justify-end mt-4">
                <Button variant="outline" onClick={() => setShowQuestions(null)}>Tutup</Button>
              </div>
            </div>
          </div>
        )}

        {showGrading && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-card dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Nilai Esai — {showGrading.quiz?.title}</h2>
              <div className="flex justify-end">
                <Button variant="outline" onClick={() => setShowGrading(null)}>Tutup</Button>
              </div>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
