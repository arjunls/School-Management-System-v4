"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { attendanceAPI, studentAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';

interface Student {
  id: number; name: string; kelas_id?: number | null;
}

interface AttendanceRow {
  id: number; student_id: number; date: string; status: string; notes?: string;
  student?: { id: number; name: string };
}

type AttendanceStatus = 'present' | 'absent' | 'sick' | 'leave';

const STATUS_LABELS: Record<AttendanceStatus, string> = {
  present: 'Hadir', absent: 'Absen', sick: 'Sakit', leave: 'Izin',
};

const STATUS_COLORS: Record<AttendanceStatus, string> = {
  present: 'bg-green-100 text-green-800 border-green-300',
  absent: 'bg-red-100 text-red-800 border-red-300',
  sick: 'bg-yellow-100 text-yellow-800 border-yellow-300',
  leave: 'bg-blue-100 text-blue-800 border-blue-300',
};

const STATUS_VARIANTS: Record<AttendanceStatus, string> = {
  present: 'success', absent: 'danger', sick: 'warning', leave: 'info',
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
          <PageHeader
            title="Presensi"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Presensi' }]}
            action={
              <div className="flex items-center gap-3">
                <Button variant="secondary" size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>}
                  onClick={() => exportAPI.download('attendance')}
                >
                  Export
                </Button>
                <Button variant="secondary" size="sm" onClick={() => setViewMode(viewMode === 'grid' ? 'list' : 'grid')}>
                  {viewMode === 'grid' ? 'Tampilan Daftar' : 'Tampilan Grid'}
                </Button>
                <Button size="sm" loading={saving} onClick={saveAll}>
                  {saving ? 'Menyimpan...' : 'Simpan Semua'}
                </Button>
              </div>
            }
          />

          <div className="flex items-center gap-3">
            <Input type="date" value={date} onChange={(e) => setDate(e.target.value)} />
            <span className="text-sm text-muted-foreground">{students.length} siswa</span>
          </div>

          {loading ? (
            <div className="text-center py-12 text-muted-foreground">Memuat presensi...</div>
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
                    <Badge variant={STATUS_VARIANTS[status] as any} size="sm" className="mt-1">{STATUS_LABELS[status]}</Badge>
                    <p className="text-[10px] mt-0.5 opacity-60">Klik untuk ganti</p>
                  </button>
                );
              })}
            </div>
          ) : (
            <div className="overflow-x-auto rounded-xl border bg-card text-card-foreground shadow-sm">
              <table className="min-w-full divide-y divide-border">
                <thead className="bg-muted/50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">Siswa</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">Status</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {students.map((student) => {
                    const status = getStatusForStudent(student.id);
                    return (
                      <tr key={student.id} className="hover:bg-muted/50">
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">{student.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <Badge variant={STATUS_VARIANTS[status] as any}>{STATUS_LABELS[status]}</Badge>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right">
                          <Button variant="ghost" size="sm" onClick={() => toggleStatus(student.id)}>Ganti</Button>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          )}

          <div className="flex flex-wrap gap-4 text-sm text-foreground/70">
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-green-500 inline-block" /> Hadir</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-red-500 inline-block" /> Absen</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-yellow-500 inline-block" /> Sakit</span>
            <span className="flex items-center gap-1"><span className="w-3 h-3 rounded-full bg-blue-500 inline-block" /> Izin</span>
          </div>
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
