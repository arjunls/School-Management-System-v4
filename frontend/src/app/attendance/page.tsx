"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { attendanceAPI, studentAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';

interface Student {
  id: number; name: string; kelas_id?: number | null;
}

interface AttendanceRow {
  id: number; student_id: number; date: string; status: string; notes?: string;
  student?: { id: number; name: string };
}

type AttendanceStatus = 'present' | 'absent' | 'sick' | 'leave';

const STATUS_LABELS: Record<AttendanceStatus, string> = {
  present: 'Present', absent: 'Absent', sick: 'Sick', leave: 'Leave',
};

const STATUS_COLORS: Record<AttendanceStatus, string> = {
  present: 'bg-green-100 text-green-800 border-green-300',
  absent: 'bg-red-100 text-red-800 border-red-300',
  sick: 'bg-yellow-100 text-yellow-800 border-yellow-300',
  leave: 'bg-blue-100 text-blue-800 border-blue-300',
};

export default function AttendancePage() {
  const { toast } = useToast();
  const [students, setStudents] = useState<Student[]>([]);
  const [records, setRecords] = useState<AttendanceRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [date, setDate] = useState(() => new Date().toISOString().split('T')[0]);
  const [attendanceMap, setAttendanceMap] = useState<Record<number, AttendanceStatus>>({});
  const [saving, setSaving] = useState(false);
  const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');

  // Fetch students
  useEffect(() => {
    const fetchStudents = async () => {
      try {
        const res = await studentAPI.getList({ per_page: 100 });
        const body = res.data as { success?: boolean; data?: unknown[] };
        const items = body?.data ?? body;
        const list = (Array.isArray(items) ? items : []) as Student[];
        setStudents(list);
      } catch { toast('Failed to load students', 'error'); }
    };
    fetchStudents();
  }, []);

  // Fetch attendance for selected date
  const fetchAttendance = useCallback(async (selectedDate: string) => {
    try {
      setLoading(true);
      const res = await attendanceAPI.getList({ date: selectedDate });
      const body = res.data as { success?: boolean; data?: unknown[] };
      const items = (Array.isArray(body?.data) ? body.data : []) as AttendanceRow[];
      setRecords(items);

      const map: Record<number, AttendanceStatus> = {};
      for (const r of items) map[r.student_id] = r.status as AttendanceStatus;
      setAttendanceMap(map);
    } catch { toast('Failed to load attendance', 'error'); }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchAttendance(date); }, [date]);

  const toggleStatus = (studentId: number) => {
    setAttendanceMap((prev) => {
      const current = prev[studentId] || 'present';
      const order: AttendanceStatus[] = ['present', 'absent', 'sick', 'leave'];
      const nextIdx = (order.indexOf(current) + 1) % order.length;
      return { ...prev, [studentId]: order[nextIdx] };
    });
  };

  const saveAll = async () => {
    setSaving(true);
    let success = 0;
    let errors = 0;

    for (const student of students) {
      const status = attendanceMap[student.id];
      if (!status) continue;

      try {
        await attendanceAPI.create({
          student_id: student.id,
          date,
          status,
        });
        success++;
      } catch {
        errors++;
      }
    }

    if (errors === 0) {
      toast(`Attendance saved for ${success} students`, 'success');
    } else {
      toast(`Saved ${success}, failed ${errors}`, 'error');
    }

    fetchAttendance(date);
    setSaving(false);
  };

  const getStatusForStudent = (studentId: number): AttendanceStatus => {
    return attendanceMap[studentId] || 'present';
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher']}>
      <MainLayout>
        <div className="space-y-6">
          {/* Header */}
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Attendance</h1>
            <div className="flex items-center gap-3">
              <button
                onClick={() => exportAPI.download('attendance')}
                className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50"
              >
                Export CSV
              </button>
              <button
                onClick={() => setViewMode(viewMode === 'grid' ? 'list' : 'grid')}
                className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50"
              >
                {viewMode === 'grid' ? 'List View' : 'Grid View'}
              </button>
              <button
                onClick={saveAll}
                disabled={saving}
                className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {saving ? 'Saving...' : 'Save All'}
              </button>
            </div>
          </div>

          {/* Date picker */}
          <div className="flex items-center gap-3">
            <label className="text-sm font-medium text-gray-700">Date:</label>
            <input
              type="date"
              className="rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
              value={date}
              onChange={(e) => setDate(e.target.value)}
            />
            <span className="text-sm text-gray-500">
              {students.length} students
            </span>
          </div>

          {/* Grid: click to cycle status */}
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading attendance...</div>
          ) : viewMode === 'grid' ? (
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
              {students.map((student) => {
                const status = getStatusForStudent(student.id);
                return (
                  <button
                    key={student.id}
                    onClick={() => toggleStatus(student.id)}
                    className={`p-3 rounded-lg border-2 text-left transition-all hover:shadow-md ${STATUS_COLORS[status]}`}
                  >
                    <p className="text-sm font-medium truncate">{student.name}</p>
                    <p className="text-xs mt-1 font-semibold">{STATUS_LABELS[status]}</p>
                    <p className="text-[10px] mt-0.5 opacity-60">Click to change</p>
                  </button>
                );
              })}
            </div>
          ) : (
            /* List view: table */
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {students.map((student) => {
                    const status = getStatusForStudent(student.id);
                    return (
                      <tr key={student.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{student.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${STATUS_COLORS[status].replace('border-', '')}`}>
                            {STATUS_LABELS[status]}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right">
                          <button
                            onClick={() => toggleStatus(student.id)}
                            className="text-indigo-600 hover:text-indigo-900 text-sm"
                          >
                            Toggle
                          </button>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          )}

          {/* Legend */}
          <div className="flex flex-wrap gap-4 text-sm text-gray-600">
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-green-500 inline-block" /> Present</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-red-500 inline-block" /> Absent</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-yellow-500 inline-block" /> Sick</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-blue-500 inline-block" /> Leave</span>
          </div>
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
