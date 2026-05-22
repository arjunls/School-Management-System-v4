"use client";
import React, { useEffect, useState } from 'react';
import { quizAPI, classAPI, subjectAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function QuizzesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [tab, setTab] = useState<'quizzes' | 'attempts' | 'take'>('quizzes');
  const [quizzes, setQuizzes] = useState<any[]>([]);
  const [attempts, setAttempts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [classes, setClasses] = useState<any[]>([]);
  const [subjects, setSubjects] = useState<any[]>([]);

  // Form state
  const [showForm, setShowForm] = useState(false);
  const [editQuiz, setEditQuiz] = useState<any>(null);
  const [form, setForm] = useState({ title: '', description: '', class_id: '', subject_id: '', time_limit: '', passing_score: '0', due_date: '', status: 'draft' });

  // Questions
  const [showQuestions, setShowQuestions] = useState<any>(null);
  const [questions, setQuestions] = useState<any[]>([]);
  const [qForm, setQForm] = useState({ question_text: '', type: 'multiple_choice', options: [] as any[], correct_answer: '', points: '1' });
  const [qOption, setQOption] = useState('');

  // Taking quiz
  const [takingQuiz, setTakingQuiz] = useState<any>(null);
  const [attempt, setAttempt] = useState<any>(null);
  const [answers, setAnswers] = useState<Record<number, string>>({});
  const [timeLeft, setTimeLeft] = useState(0);

  // Grading
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

  // Timer
  useEffect(() => {
    if (timeLeft <= 0 || tab !== 'take') return;
    const t = setInterval(() => setTimeLeft(p => { if (p <= 1) { handleSubmitQuiz(); return 0; } return p - 1; }), 1000);
    return () => clearInterval(t);
  }, [timeLeft, tab]);

  const fmtTime = (s: number) => `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Quizzes</h1>
            <div className="flex gap-2">
              <button onClick={() => setTab('quizzes')} className={`px-3 py-2 text-sm rounded-md ${tab === 'quizzes' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 dark:text-gray-200'}`}>Quizzes</button>
              <button onClick={() => setTab('attempts')} className={`px-3 py-2 text-sm rounded-md ${tab === 'attempts' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 dark:text-gray-200'}`}>Attempts</button>
            </div>
          </div>

          {tab === 'take' && takingQuiz ? (
            <div>
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-semibold dark:text-white">{takingQuiz.title}</h2>
                {timeLeft > 0 && <span className="text-lg font-mono text-red-600">{fmtTime(timeLeft)}</span>}
              </div>
              <div className="space-y-4">
                {takingQuiz.questions?.map((q: any) => (
                  <div key={q.id} className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
                    <p className="font-medium mb-2 dark:text-white">{q.question_text} <span className="text-sm text-gray-400">({q.points}pt)</span></p>
                    {q.type === 'multiple_choice' ? (
                      <div className="space-y-2">
                        {(q.options ?? []).map((opt: any, i: number) => (
                          <label key={i} className="flex items-center gap-2 text-sm dark:text-gray-200 cursor-pointer">
                            <input type="radio" name={`q_${q.id}`} value={opt.key} checked={answers[q.id] === opt.key} onChange={e => setAnswers(p => ({ ...p, [q.id]: e.target.value }))} className="accent-indigo-600" />
                            {opt.value}
                          </label>
                        ))}
                      </div>
                    ) : (
                      <textarea value={answers[q.id] ?? ''} onChange={e => setAnswers(p => ({ ...p, [q.id]: e.target.value }))} rows={3} className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    )}
                  </div>
                ))}
                <button onClick={handleSubmitQuiz} className="px-6 py-2 bg-indigo-600 text-white rounded-md">Submit</button>
              </div>
            </div>
          ) : tab === 'quizzes' ? (
            <>
              {user?.role !== 'student' && <button onClick={() => { setEditQuiz(null); setForm({ title: '', description: '', class_id: '', subject_id: '', time_limit: '', passing_score: '0', due_date: '', status: 'draft' }); setShowForm(true); }} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Create Quiz</button>}
              {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
                quizzes.length === 0 ? <div className="text-center py-12 text-gray-500 dark:text-gray-400">No quizzes</div> :
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                  {quizzes.map(q => (
                    <div key={q.id} className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
                      <div className="flex items-center justify-between mb-1">
                        <h3 className="font-semibold dark:text-white">{q.title}</h3>
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${q.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 dark:bg-slate-600 dark:text-gray-200'}`}>{q.status}</span>
                      </div>
                      <p className="text-xs text-gray-500 dark:text-gray-400">{q.class?.name} — {q.subject?.name}</p>
                      {q.time_limit && <p className="text-xs text-gray-400 mt-1 dark:text-gray-500">Time: {q.time_limit}min</p>}
                      <div className="mt-3 flex gap-2 justify-end">
                        {user?.role === 'student' ? (
                          q.status === 'published' && <button onClick={() => handleStartQuiz(q.id)} className="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-md">Start</button>
                        ) : (
                          <>
                            <button onClick={() => loadQuestions(q.id)} className="px-3 py-1.5 text-xs border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Questions</button>
                            <button onClick={() => { setEditQuiz(q); setForm({ title: q.title, description: q.description ?? '', class_id: String(q.class_id), subject_id: String(q.subject_id), time_limit: String(q.time_limit ?? ''), passing_score: String(q.passing_score ?? '0'), due_date: q.due_date ? q.due_date.slice(0,16) : '', status: q.status }); setShowForm(true); }} className="px-3 py-1.5 text-xs border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Edit</button>
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
              {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
                attempts.length === 0 ? <div className="text-center py-12 text-gray-500 dark:text-gray-400">No attempts</div> :
                <div className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 overflow-hidden">
                  <table className="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
                    <thead className="bg-gray-50 dark:bg-slate-700"><tr>
                      <th className="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Quiz</th>
                      {user?.role !== 'student' && <th className="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Student</th>}
                      <th className="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-300">Score</th>
                      <th className="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-300">Status</th>
                      <th className="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-300">Actions</th>
                    </tr></thead>
                    <tbody className="divide-y divide-gray-200 dark:divide-slate-700">
                      {attempts.map(a => (
                        <tr key={a.id} className="dark:text-gray-200">
                          <td className="px-4 py-3">{a.quiz?.title}</td>
                          {user?.role !== 'student' && <td className="px-4 py-3">{a.student?.name}</td>}
                          <td className="px-4 py-3 text-center">{a.score ?? '—'}</td>
                          <td className="px-4 py-3 text-center">
                            <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${a.status === 'graded' ? 'bg-green-100 text-green-800' : a.status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'}`}>{a.status}</span>
                          </td>
                          <td className="px-4 py-3 text-right">
                            {user?.role !== 'student' && a.status === 'submitted' && <button onClick={async () => { try { const res = await quizAPI.get(a.quiz_id); const quiz = res.data?.data; const res2 = await quizAPI.get(a.quiz_id); /* load full attempt details */ const initScores: Record<number, string> = {}; setShowGrading(a); setGradeScores(initScores); } catch {} }} className="text-indigo-600 hover:text-indigo-800">Grade</button>}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              }
            </>
          )}
        </div>

        {/* Quiz Form Modal */}
        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{editQuiz ? 'Edit' : 'Create'} Quiz</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Title" required className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <select value={form.class_id} onChange={e => setForm(p => ({ ...p, class_id: e.target.value }))} required className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    <option value="">Class</option>{classes.map((c: any) => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                  <select value={form.subject_id} onChange={e => setForm(p => ({ ...p, subject_id: e.target.value }))} required className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    <option value="">Subject</option>{subjects.map((s: any) => <option key={s.id} value={s.id}>{s.name}</option>)}
                  </select>
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="number" value={form.time_limit} onChange={e => setForm(p => ({ ...p, time_limit: e.target.value }))} placeholder="Time limit (min)" className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <input type="number" value={form.passing_score} onChange={e => setForm(p => ({ ...p, passing_score: e.target.value }))} placeholder="Passing score" className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="datetime-local" value={form.due_date} onChange={e => setForm(p => ({ ...p, due_date: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <select value={form.status} onChange={e => setForm(p => ({ ...p, status: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    <option value="draft">Draft</option><option value="published">Published</option>
                  </select>
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">{editQuiz ? 'Update' : 'Create'}</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Questions Modal */}
        {showQuestions && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-2xl bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6 max-h-[80vh] overflow-y-auto">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{showQuestions.title} — Questions</h2>
              <div className="space-y-3 mb-4">
                {questions.map(q => (
                  <div key={q.id} className="border dark:border-slate-600 rounded-lg p-3">
                    <div className="flex justify-between items-start">
                      <div className="flex-1 text-sm dark:text-gray-200">
                        <p>{q.question_text} <span className="text-gray-400">({q.points}pt, {q.type})</span></p>
                        {q.type === 'multiple_choice' && q.options && <div className="mt-1 text-xs text-gray-500">{q.options.map((o: any, i: number) => <span key={i} className={`mr-2 ${o.key === q.correct_answer ? 'text-green-600 font-medium' : ''}`}>{o.key}: {o.value}</span>)}</div>}
                      </div>
                      <button onClick={() => handleDeleteQuestion(q.id)} className="text-red-500 text-xs">Delete</button>
                    </div>
                  </div>
                ))}
              </div>
              <div className="border-t dark:border-slate-700 pt-4">
                <h3 className="text-sm font-semibold mb-2 dark:text-white">Add Question</h3>
                <div className="space-y-3">
                  <textarea value={qForm.question_text} onChange={e => setQForm(p => ({ ...p, question_text: e.target.value }))} placeholder="Question text" rows={2} className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <div className="grid grid-cols-2 gap-3">
                    <select value={qForm.type} onChange={e => setQForm(p => ({ ...p, type: e.target.value, options: [], correct_answer: '' }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                      <option value="multiple_choice">Multiple Choice</option><option value="essay">Essay</option>
                    </select>
                    <input type="number" value={qForm.points} onChange={e => setQForm(p => ({ ...p, points: e.target.value }))} placeholder="Points" min={1} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  </div>
                  {qForm.type === 'multiple_choice' && (
                    <div className="space-y-2">
                      {qForm.options.map((o, i) => (
                        <div key={i} className="flex items-center gap-2">
                          <span className="text-xs font-medium dark:text-gray-200">{o.key}</span>
                          <input type="text" value={o.value} onChange={e => { const opts = [...qForm.options]; opts[i].value = e.target.value; setQForm(p => ({ ...p, options: opts })); }} className="flex-1 rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" />
                          <input type="radio" name="correct" checked={qForm.correct_answer === o.key} onChange={() => setQForm(p => ({ ...p, correct_answer: o.key }))} className="accent-indigo-600" />
                        </div>
                      ))}
                      <button type="button" onClick={() => setQForm(p => ({ ...p, options: [...p.options, { key: String.fromCharCode(65 + p.options.length), value: '' }] }))} className="text-xs text-indigo-600">+ Add option</button>
                    </div>
                  )}
                  <button onClick={handleAddQuestion} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Add</button>
                </div>
              </div>
              <div className="flex justify-end mt-4">
                <button onClick={() => setShowQuestions(null)} className="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Close</button>
              </div>
            </div>
          </div>
        )}

        {/* Grading Modal */}
        {showGrading && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">Grade Essay — {showGrading.quiz?.title}</h2>
              <div className="flex justify-end">
                <button onClick={() => setShowGrading(null)} className="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Close</button>
              </div>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
