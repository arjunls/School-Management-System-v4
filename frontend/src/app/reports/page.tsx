"use client";
import React, { useState } from 'react';
import { reportAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';

export default function ReportsPage() {
  const { toast } = useToast();
  const [studentId, setStudentId] = useState('');
  const [report, setReport] = useState<any>(null);
  const [loading, setLoading] = useState(false);

  const fetchReport = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!studentId) return;
    setLoading(true);
    setReport(null);
    try {
      const res = await reportAPI.studentReportCard(Number(studentId));
      setReport(res.data?.data ?? null);
    } catch {
      toast('Failed to load report', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handlePrint = () => window.print();

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <h1 className="text-2xl font-bold text-gray-900">Student Report Card</h1>

          <form onSubmit={fetchReport} className="flex items-end gap-3">
            <div className="flex-1 max-w-xs">
              <label className="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
              <input type="number" value={studentId} onChange={e => setStudentId(e.target.value)} placeholder="Enter student ID"
                className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none" required />
            </div>
            <button type="submit" disabled={loading}
              className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
              {loading ? 'Loading...' : 'Generate Report'}
            </button>
          </form>

          {report && (
            <div className="bg-white rounded-lg shadow border p-6 print:p-0 print:shadow-none print:border-none">
              <div className="flex justify-between items-center mb-6 print:hidden">
                <h2 className="text-xl font-semibold">Report Card</h2>
                <button onClick={handlePrint} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Print / PDF</button>
              </div>

              <div className="border-b pb-4 mb-4">
                <h3 className="text-lg font-bold">{report.student?.name}</h3>
                <p className="text-sm text-gray-500">
                  NISN: {report.student?.nisn || '—'} | Class: {report.student?.kelas?.name || '—'} | Email: {report.student?.email}
                </p>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div className="bg-blue-50 p-3 rounded-lg text-center">
                  <p className="text-xs text-gray-500">Average Score</p>
                  <p className="text-xl font-bold text-blue-700">{report.average_score ?? 'N/A'}</p>
                </div>
                <div className="bg-green-50 p-3 rounded-lg text-center">
                  <p className="text-xs text-gray-500">Attendance Rate</p>
                  <p className="text-xl font-bold text-green-700">{report.attendance_summary?.attendance_rate ?? 0}%</p>
                </div>
                <div className="bg-orange-50 p-3 rounded-lg text-center">
                  <p className="text-xs text-gray-500">Present</p>
                  <p className="text-xl font-bold text-orange-700">{report.attendance_summary?.present ?? 0}/{report.attendance_summary?.total_days ?? 0}</p>
                </div>
                <div className="bg-red-50 p-3 rounded-lg text-center">
                  <p className="text-xs text-gray-500">Absent</p>
                  <p className="text-xl font-bold text-red-700">{report.attendance_summary?.absent ?? 0}</p>
                </div>
              </div>

              {Object.entries(report.grades ?? {}).map(([term, gradeList]: [string, any]) => (
                <div key={term} className="mb-6">
                  <h4 className="font-semibold text-gray-700 mb-2">{term}</h4>
                  <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="bg-gray-50">
                      <tr>
                        <th className="px-4 py-2 text-left font-medium text-gray-500">Subject</th>
                        <th className="px-4 py-2 text-center font-medium text-gray-500">Score</th>
                        <th className="px-4 py-2 text-center font-medium text-gray-500">Grade</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {(gradeList as any[]).map((g: any, i: number) => (
                        <tr key={i}>
                          <td className="px-4 py-2">{g.subject?.name}</td>
                          <td className="px-4 py-2 text-center">{g.score ?? '—'}</td>
                          <td className="px-4 py-2 text-center font-semibold">{g.grade || '—'}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ))}
            </div>
          )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
