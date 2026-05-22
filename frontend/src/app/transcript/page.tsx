"use client";
import React, { useEffect, useState } from 'react';
import { reportAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function TranscriptPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [studentId, setStudentId] = useState('');
  const [transcript, setTranscript] = useState<any>(null);
  const [students, setStudents] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (user?.role === 'student') { setStudentId(String(user.id)); fetchTranscript(String(user.id)); }
    else { studentAPI.getList({ per_page: 50 }).then(r => setStudents(r.data?.data ?? [])).catch(() => {}); }
  }, [user]);

  const fetchTranscript = async (id: string) => {
    if (!id) return;
    setLoading(true); setTranscript(null);
    try { const res = await reportAPI.transcript(Number(id)); setTranscript(res.data?.data); }
    catch { toast('Failed to load transcript', 'error'); }
    finally { setLoading(false); }
  };

  const handlePrint = () => window.print();

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Student Transcript</h1>
            {transcript && <button onClick={handlePrint} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md print:hidden">Print</button>}
          </div>

          {user?.role !== 'student' && (
            <div className="flex gap-3 print:hidden">
              <select value={studentId} onChange={e => setStudentId(e.target.value)} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <option value="">Select Student</option>
                {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
              </select>
              <button onClick={() => fetchTranscript(studentId)} disabled={!studentId} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md disabled:opacity-50">Load</button>
            </div>
          )}

          {loading ? <div className="text-center py-12 text-gray-500 dark:text-gray-400">Loading...</div> :
            transcript ? (
              <div className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-8 max-w-4xl mx-auto">
                <div className="text-center border-b dark:border-slate-700 pb-6 mb-6">
                  <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Academic Transcript</h2>
                  <p className="text-lg text-gray-600 dark:text-gray-300 mt-1">{transcript.student?.name}</p>
                  <p className="text-sm text-gray-400 dark:text-gray-400">{transcript.student?.email}</p>
                </div>

                <table className="w-full text-sm mb-6">
                  <thead>
                    <tr className="border-b dark:border-slate-700">
                      <th className="py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Subject</th>
                      <th className="py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Total Scores</th>
                      <th className="py-2 text-center font-semibold text-gray-700 dark:text-gray-200">Average</th>
                    </tr>
                  </thead>
                  <tbody>
                    {transcript.subjects?.map((s: any, i: number) => (
                      <tr key={i} className="border-b dark:border-slate-700">
                        <td className="py-2 text-gray-800 dark:text-gray-200">{s.subject}</td>
                        <td className="py-2 text-center text-gray-600 dark:text-gray-300">{s.total}</td>
                        <td className="py-2 text-center font-medium text-gray-800 dark:text-gray-200">{s.average ?? '—'}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>

                <div className="grid grid-cols-3 gap-4 text-center">
                  <div className="bg-gray-50 dark:bg-slate-700 rounded-lg p-3">
                    <p className="text-sm text-gray-500 dark:text-gray-400">Overall Average</p>
                    <p className="text-xl font-bold text-indigo-600">{transcript.overall_average ?? '—'}</p>
                  </div>
                  <div className="bg-gray-50 dark:bg-slate-700 rounded-lg p-3">
                    <p className="text-sm text-gray-500 dark:text-gray-400">Attendance Rate</p>
                    <p className="text-xl font-bold text-green-600">{transcript.attendance_rate ?? '—'}%</p>
                  </div>
                  <div className="bg-gray-50 dark:bg-slate-700 rounded-lg p-3">
                    <p className="text-sm text-gray-500 dark:text-gray-400">Subjects</p>
                    <p className="text-xl font-bold text-purple-600">{transcript.subjects?.length ?? 0}</p>
                  </div>
                </div>
              </div>
            ) : (
              <div className="text-center py-12 text-gray-500 dark:text-gray-400">Select a student to view transcript</div>
            )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
