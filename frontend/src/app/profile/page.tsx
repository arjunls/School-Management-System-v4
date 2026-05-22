"use client";
import React, { Suspense, useEffect, useState } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import { studentAPI, gradeAPI, attendanceAPI, scheduleAPI, classAPI } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';

interface Kelas { id: number; name: string; grade_level: number; }
interface StudentUser {
  id: number; name: string; email: string; nisn?: string; phone?: string;
  address?: string; date_of_birth?: string; gender?: string; status?: string;
  kelas?: Kelas | null;
}

interface Grade { id: number; subject_id: number; score?: number; grade?: string; term?: string; subject?: { id: number; name: string; code: string } | null; }

interface AttendanceRow {
  id: number; student_id: number; date: string; status: string; notes?: string;
}

interface ScheduleRow {
  id: number; day_of_week: string; start_time: string; end_time: string; room?: string;
  subject?: { id: number; name: string; code: string } | null;
  teacher?: { id: number; name: string } | null;
}

const STATUS_STYLES: Record<string, string> = {
  present: 'bg-green-100 text-green-800', absent: 'bg-red-100 text-red-800',
  sick: 'bg-yellow-100 text-yellow-800', leave: 'bg-blue-100 text-blue-800',
};

const DAY_ORDER: Record<string, number> = {
  monday: 1, tuesday: 2, wednesday: 3, thursday: 4, friday: 5, saturday: 6,
};

function ProfileContent() {
  const { user } = useAuth();
  const { toast } = useToast();
  const searchParams = useSearchParams();
  const router = useRouter();
  const studentId = searchParams.get('id');

  const [student, setStudent] = useState<StudentUser | null>(null);
  const [grades, setGrades] = useState<Grade[]>([]);
  const [attendance, setAttendance] = useState<AttendanceRow[]>([]);
  const [schedules, setSchedules] = useState<ScheduleRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchId, setSearchId] = useState(studentId || '');

  const isAdmin = user?.role === 'admin';
  const isTeacher = user?.role === 'teacher';
  const viewingSelf = !studentId || String(user?.id) === studentId;

  const targetId = studentId || String(user?.id);

  useEffect(() => {
    if (!targetId) return;
    const fetchProfile = async () => {
      setLoading(true);
      try {
        const [studentRes, gradeRes, attRes, schedRes] = await Promise.all([
          studentAPI.getById(targetId),
          gradeAPI.getList({ student_id: targetId, per_page: 50 }),
          attendanceAPI.getList({ student_id: targetId, per_page: 30 }),
          (async () => {
            // First get the student to find their kelas_id, then fetch schedules
            try {
              const s = await studentAPI.getById(targetId);
              const body = s.data as { success?: boolean; data?: StudentUser };
              const studentData = body?.data || body;
              if ((studentData as StudentUser)?.kelas?.id) {
                const sc = await scheduleAPI.getList({ class_id: (studentData as StudentUser).kelas!.id });
                const scBody = sc.data as { success?: boolean; data?: unknown[] };
                return (scBody?.data || scBody || []) as ScheduleRow[];
              }
            } catch { /* no schedules */ }
            return [];
          })(),
        ]);

        const stuBody = studentRes.data as { success?: boolean; data?: StudentUser };
        setStudent(stuBody?.data || (stuBody as unknown as StudentUser));

        const gBody = gradeRes.data as { success?: boolean; data?: unknown[] };
        setGrades((Array.isArray(gBody?.data) ? gBody.data : []) as Grade[]);

        const aBody = attRes.data as { success?: boolean; data?: unknown[] };
        setAttendance((Array.isArray(aBody?.data) ? aBody.data : []) as AttendanceRow[]);

        setSchedules(await schedRes);
      } catch {
        toast('Failed to load profile', 'error');
      } finally { setLoading(false); }
    };
    fetchProfile();
  }, [targetId]);

  const handleSearchStudent = () => {
    if (searchId) {
      router.push(`/profile?id=${searchId}`);
    }
  };

  const sortedSchedules = [...schedules].sort((a, b) => {
    const d = (DAY_ORDER[a.day_of_week] || 0) - (DAY_ORDER[b.day_of_week] || 0);
    if (d !== 0) return d;
    return a.start_time.localeCompare(b.start_time);
  });

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          {/* Header */}
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">
              {viewingSelf ? 'My Profile' : 'Student Profile'}
            </h1>
            {(isAdmin || isTeacher) && (
              <div className="flex items-center gap-2">
                <input
                  type="number"
                  placeholder="Student ID..."
                  className="block rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                  value={searchId}
                  onChange={(e) => setSearchId(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && handleSearchStudent()}
                />
                <button onClick={handleSearchStudent} className="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">View</button>
              </div>
            )}
          </div>

          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading profile...</div>
          ) : !student ? (
            <div className="text-center py-12 text-gray-500">Student not found.</div>
          ) : (
            <>
              {/* Student Info Card */}
              <div className="bg-white rounded-lg shadow border p-6">
                <div className="flex items-start gap-6">
                  <div className="flex-shrink-0 h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-2xl font-bold">
                    {student.name.charAt(0).toUpperCase()}
                  </div>
                  <div className="flex-1 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Name</p>
                      <p className="text-sm font-medium text-gray-900">{student.name}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">NISN</p>
                      <p className="text-sm font-medium text-gray-900">{student.nisn || '—'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Email</p>
                      <p className="text-sm text-gray-600">{student.email}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Class</p>
                      <p className="text-sm font-medium text-gray-900">{student.kelas?.name || '—'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Gender</p>
                      <p className="text-sm text-gray-600 capitalize">{student.gender || '—'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Phone</p>
                      <p className="text-sm text-gray-600">{student.phone || '—'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Date of Birth</p>
                      <p className="text-sm text-gray-600">{student.date_of_birth || '—'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500 uppercase">Status</p>
                      <span className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${student.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                        {student.status || 'active'}
                      </span>
                    </div>
                    <div className="md:col-span-1">
                      <p className="text-xs text-gray-500 uppercase">Address</p>
                      <p className="text-sm text-gray-600">{student.address || '—'}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Grades */}
                <div className="bg-white rounded-lg shadow border p-6">
                  <h3 className="text-lg font-semibold mb-4">Grades</h3>
                  {grades.length === 0 ? (
                    <p className="text-sm text-gray-400">No grades recorded.</p>
                  ) : (
                    <div className="overflow-x-auto">
                      <table className="min-w-full divide-y divide-gray-200 text-sm">
                        <thead className="bg-gray-50">
                          <tr>
                            <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                          {grades.map((g) => (
                            <tr key={g.id} className="hover:bg-gray-50">
                              <td className="px-3 py-2 text-gray-900">{g.subject?.name || `#${g.subject_id}`}</td>
                              <td className="px-3 py-2 text-gray-600">{g.score ?? '—'}</td>
                              <td className="px-3 py-2 font-medium">{g.grade || '—'}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  )}
                </div>

                {/* Attendance */}
                <div className="bg-white rounded-lg shadow border p-6">
                  <h3 className="text-lg font-semibold mb-4">Attendance History</h3>
                  {attendance.length === 0 ? (
                    <p className="text-sm text-gray-400">No attendance records.</p>
                  ) : (
                    <div className="overflow-x-auto max-h-64 overflow-y-auto">
                      <table className="min-w-full divide-y divide-gray-200 text-sm">
                        <thead className="bg-gray-50 sticky top-0">
                          <tr>
                            <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                          {attendance.map((a) => (
                            <tr key={a.id} className="hover:bg-gray-50">
                              <td className="px-3 py-2 text-gray-900">{a.date}</td>
                              <td className="px-3 py-2">
                                <span className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${STATUS_STYLES[a.status] || 'bg-gray-100 text-gray-800'}`}>
                                  {a.status}
                                </span>
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  )}
                </div>
              </div>

              {/* Class Schedule */}
              <div className="bg-white rounded-lg shadow border p-6">
                <h3 className="text-lg font-semibold mb-4">Class Schedule</h3>
                {schedules.length === 0 ? (
                  <p className="text-sm text-gray-400">No schedule available.</p>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200 text-sm">
                      <thead className="bg-gray-50">
                        <tr>
                          <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                          <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                          <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                          <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                          <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-200">
                        {sortedSchedules.map((s) => (
                          <tr key={s.id} className="hover:bg-gray-50">
                            <td className="px-4 py-2 text-gray-900 capitalize">{s.day_of_week}</td>
                            <td className="px-4 py-2 text-gray-600">{s.start_time.slice(0,5)} – {s.end_time.slice(0,5)}</td>
                            <td className="px-4 py-2 text-gray-900">{s.subject?.name || '—'}</td>
                            <td className="px-4 py-2 text-gray-600">{s.teacher?.name || '—'}</td>
                            <td className="px-4 py-2 text-gray-600">{s.room || '—'}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </div>
            </>
          )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}

export default function ProfilePage() {
  return (
    <Suspense fallback={<div className="text-center py-12 text-gray-500">Loading profile...</div>}>
      <ProfileContent />
    </Suspense>
  );
}
