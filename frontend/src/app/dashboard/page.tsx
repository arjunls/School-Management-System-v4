"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { dashboardAPI, gradeAPI, attendanceAPI, scheduleAPI, studentAPI, classAPI, teacherAPI, subjectAPI, parentAPI, academicYearAPI, feeAPI, assignmentAPI, libraryAPI } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { MainLayout } from '@/components/layout/MainLayout';
import { Header } from '@/components/layout/Header';
import { StatCard } from '@/components/widgets/StatCard';
import { SkeletonCard } from '@/components/ui/SkeletonLoader';
import { LineChart } from '@/components/widgets/LineChart';

interface AttendanceChart { labels: string[]; present: number[]; absent: number[]; sick: number[]; }
interface PerformanceChart { labels: string[]; data: number[]; }

function BarChart({ data, labels, color, height = 200 }: { data: number[]; labels: string[]; color: string; height?: number }) {
  const max = Math.max(...data, 1);
  const w = 40; const gap = 12;
  const totalW = (w + gap) * data.length - gap;
  return (
    <svg width="100%" height={height} viewBox={`0 0 ${Math.max(totalW, 200)} ${height}`} preserveAspectRatio="xMidYMid meet">
      {data.map((v, i) => {
        const barH = (v / max) * (height - 30);
        const x = i * (w + gap);
        const y = height - 20 - barH;
        return (
          <g key={i}>
            <rect x={x} y={y} width={w} height={barH} rx="4" fill={color} opacity={0.85}><title>{v}</title></rect>
            <text x={x + w / 2} y={height - 4} textAnchor="middle" fontSize="10" fill="#6b7280">{labels[i]}</text>
            <text x={x + w / 2} y={y - 5} textAnchor="middle" fontSize="10" fill="#374151" fontWeight="600">{v}</text>
          </g>
        );
      })}
    </svg>
  );
}

function StackedBarChart({ labels, present, absent, sick, height = 200 }: { labels: string[]; present: number[]; absent: number[]; sick: number[]; height?: number }) {
  const max = Math.max(...present.map((_, i) => present[i] + absent[i] + sick[i]), 1);
  const w = 36; const gap = 10;
  const totalW = (w + gap) * labels.length - gap;
  return (
    <svg width="100%" height={height} viewBox={`0 0 ${Math.max(totalW, 200)} ${height}`} preserveAspectRatio="xMidYMid meet">
      {labels.map((label, i) => {
        const hScale = (v: number) => (v / max) * (height - 30);
        const x = i * (w + gap);
        const baseY = height - 20;
        return (
          <g key={i}>
            <rect x={x} y={baseY - hScale(present[i]) - hScale(absent[i]) - hScale(sick[i])} width={w} height={hScale(sick[i])} rx="2" fill="#f59e0b" opacity={0.85}><title>Sick: {sick[i]}</title></rect>
            <rect x={x} y={baseY - hScale(present[i]) - hScale(absent[i])} width={w} height={hScale(absent[i])} rx="2" fill="#ef4444" opacity={0.85}><title>Absent: {absent[i]}</title></rect>
            <rect x={x} y={baseY - hScale(present[i])} width={w} height={hScale(present[i])} rx="2" fill="#22c55e" opacity={0.85}><title>Present: {present[i]}</title></rect>
            <text x={x + w / 2} y={baseY + 12} textAnchor="middle" fontSize="9" fill="#6b7280">{label}</text>
          </g>
        );
      })}
    </svg>
  );
}

/* ── Student Dashboard ── */
function StudentDashboard() {
  const { user } = useAuth();
  const [grades, setGrades] = useState<{ score?: number; subject?: { name: string } | null }[]>([]);
  const [attendance, setAttendance] = useState<{ status: string }[]>([]);
  const [schedules, setSchedules] = useState<{ day_of_week: string; start_time: string; end_time: string; subject?: { name: string } | null; room?: string | null }[]>([]);
  const [invoices, setInvoices] = useState<any[]>([]);
  const [assignments, setAssignments] = useState<any[]>([]);
  const [loans, setLoans] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [gRes, aRes, sRes, invRes, asgRes, loanRes] = await Promise.all([
          gradeAPI.getList({ student_id: user?.id }),
          attendanceAPI.getList({ student_id: user?.id }),
          (async () => {
            const s = await studentAPI.getById(String(user?.id));
            const sBody = s.data as { success?: boolean; data?: { kelas?: { id: number } | null } };
            const kelasId = sBody?.data?.kelas?.id;
            if (kelasId) {
              const sc = await scheduleAPI.getList({ class_id: kelasId });
              const scBody = sc.data as { success?: boolean; data?: unknown[] };
              return Array.isArray(scBody?.data) ? scBody.data : [];
            }
            return [];
          })(),
          feeAPI.getInvoices({ student_id: user?.id }),
          assignmentAPI.getList({ student_id: user?.id }),
          libraryAPI.getLoans({ per_page: 5 }),
        ]);

        const ex = (r: unknown) => { const b = r as { success?: boolean; data?: unknown[] }; return Array.isArray(b?.data) ? b.data : []; };
        setGrades(ex(gRes.data) as { score?: number; subject?: { name: string } | null }[]);
        setAttendance(ex(aRes.data) as { status: string }[]);
        setSchedules(ex(sRes) as { day_of_week: string; start_time: string; end_time: string; subject?: { name: string } | null; room?: string | null }[]);
        setInvoices(ex(invRes.data));
        setAssignments(ex(asgRes.data));
        setLoans(ex(loanRes.data?.data ?? loanRes.data));
      } catch { /* */ }
      finally { setLoading(false); }
    })();
  }, [user]);

  if (loading) return <div className="text-center py-12 text-gray-500">Loading dashboard...</div>;

  const presentCount = attendance.filter((a) => a.status === 'present').length;
  const totalAttendance = attendance.length;
  const attRate = totalAttendance ? Math.round((presentCount / totalAttendance) * 100) : null;
  const dueAssignments = assignments.filter(a => a.status !== 'submitted');
  const pendingInvoices = invoices.filter(i => i.status !== 'paid');

  return (
    <div className="space-y-6">
      <h2 className="text-xl font-semibold text-gray-800">Welcome, {user?.name}</h2>
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <StatCard title="My Grades" value={grades.length} color="blue" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fillRule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clipRule="evenodd"/></svg>} />
        <StatCard title="Attendance" value={attRate !== null ? `${attRate}%` : 'N/A'} color="green" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/></svg>} />
        <StatCard title="Pending Invoices" value={pendingInvoices.length} color="yellow" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fillRule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1z" clipRule="evenodd"/></svg>} />
        <StatCard title="Due Tasks" value={dueAssignments.length} color="red" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/></svg>} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">My Grades</h3>
          {grades.length === 0 ? <p className="text-sm text-gray-400">No grades yet.</p> : (
            <div className="overflow-x-auto text-sm">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50"><tr><th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th><th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Score</th></tr></thead>
                <tbody className="divide-y divide-gray-200">
                  {grades.map((g, i) => (
                    <tr key={i} className="hover:bg-gray-50">
                      <td className="px-3 py-2 text-gray-900">{g.subject?.name || '—'}</td>
                      <td className="px-3 py-2 font-medium">{g.score ?? '—'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>

        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">Upcoming Schedule</h3>
          {schedules.length === 0 ? <p className="text-sm text-gray-400">No schedule.</p> : (
            <div className="overflow-x-auto text-sm">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50"><tr><th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day</th><th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th><th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th></tr></thead>
                <tbody className="divide-y divide-gray-200">
                  {schedules.slice(0, 5).map((s, i) => (
                    <tr key={i} className="hover:bg-gray-50">
                      <td className="px-3 py-2 text-gray-900 capitalize">{s.day_of_week}</td>
                      <td className="px-3 py-2 text-gray-600">{s.start_time.slice(0,5)}–{s.end_time.slice(0,5)}</td>
                      <td className="px-3 py-2 text-gray-900">{s.subject?.name || '—'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>

        <div className="bg-white rounded-lg shadow border p-4">
          <h3 className="text-sm font-semibold mb-3">Pending Assignments</h3>
          {dueAssignments.length === 0 ? <p className="text-xs text-gray-400">All caught up!</p> : (
            <div className="space-y-2 text-sm">
              {dueAssignments.slice(0, 4).map((a: any) => (
                <div key={a.id} className="flex justify-between items-center border-b pb-1">
                  <span className="text-gray-800">{a.title ?? '—'}</span>
                  <span className="text-xs text-gray-400">{a.due_date ? new Date(a.due_date).toLocaleDateString() : ''}</span>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="bg-white rounded-lg shadow border p-4">
          <h3 className="text-sm font-semibold mb-3">Current Loans</h3>
          {loans.length === 0 ? <p className="text-xs text-gray-400">No borrowed books.</p> : (
            <div className="space-y-2 text-sm">
              {loans.map((l: any) => (
                <div key={l.id} className="flex justify-between items-center border-b pb-1">
                  <span className="text-gray-800">{l.book?.title ?? '—'}</span>
                  <span className={`text-xs ${l.status === 'overdue' ? 'text-red-600 font-medium' : 'text-gray-400'}`}>{l.status}</span>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

/* ── Teacher Dashboard ── */
function TeacherDashboard() {
  const { user } = useAuth();
  const [classes, setClasses] = useState<{ id: number; name: string; students_count?: number }[]>([]);
  const [subjects, setSubjects] = useState<{ id: number; name: string; code: string }[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [cRes, sRes] = await Promise.all([
          classAPI.getList({ homeroom_teacher_id: user?.id }),
          subjectAPI.getList({ teacher_id: user?.id }),
        ]);
        const ex = (r: unknown) => { const b = r as { success?: boolean; data?: unknown[] }; return Array.isArray(b?.data) ? b.data : []; };
        setClasses(ex(cRes.data) as { id: number; name: string; students_count?: number }[]);
        setSubjects(ex(sRes.data) as { id: number; name: string; code: string }[]);
      } catch { /* */ }
      finally { setLoading(false); }
    })();
  }, [user]);

  if (loading) return <div className="text-center py-12 text-gray-500">Loading dashboard...</div>;

  const totalStudents = classes.reduce((sum, c) => sum + (c.students_count || 0), 0);

  return (
    <div className="space-y-6">
      <h2 className="text-xl font-semibold text-gray-800">Welcome, {user?.name}</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <StatCard title="My Classes" value={classes.length} color="blue" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a4 4 0 00-4 4v1a5 5 0 00-4.472 3.341A5.972 5.972 0 005 12a1 1 0 100 2h10a1 1 0 100-2c0-1.657-.665-3.143-1.528-3.659A5 5 0 0014 5V4a4 4 0 00-4-4z"/></svg>} />
        <StatCard title="My Subjects" value={subjects.length} color="green" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804z"/></svg>} />
        <StatCard title="Total Students" value={totalStudents} color="purple" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">My Classes</h3>
          {classes.length === 0 ? <p className="text-sm text-gray-400">No classes assigned.</p> : (
            <div className="space-y-3">
              {classes.map((c) => (
                <div key={c.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span className="font-medium text-gray-900">{c.name}</span>
                  <span className="text-sm text-gray-500">{c.students_count ?? 0} students</span>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">My Subjects</h3>
          {subjects.length === 0 ? <p className="text-sm text-gray-400">No subjects assigned.</p> : (
            <div className="space-y-3">
              {subjects.map((s) => (
                <div key={s.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span><span className="font-medium text-gray-900">{s.name}</span> <span className="text-xs text-gray-400 ml-1">({s.code})</span></span>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

/* ── Admin Dashboard ── */
function AdminDashboard() {
  const [stats, setStats] = useState({ students: 0, teachers: 0, classes: 0, attendanceRate: 0 });
  const [attendanceChart, setAttendanceChart] = useState<AttendanceChart | null>(null);
  const [performanceChart, setPerformanceChart] = useState<PerformanceChart | null>(null);
  const [academicYears, setAcademicYears] = useState<any[]>([]);
  const [selectedYear, setSelectedYear] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [studentId, setStudentId] = useState('');
  const [trend, setTrend] = useState<any>(null);

  const fetchData = useCallback(async (yearId?: string) => {
    try {
      setLoading(true);
      const params = yearId ? { academic_year_id: yearId } : {};
      const [statsRes, attRes, perfRes] = await Promise.all([
        dashboardAPI.getStats(params), dashboardAPI.getAttendanceChartData(params), dashboardAPI.getPerformanceChartData(params),
      ]);
      if (statsRes.data?.success) setStats(statsRes.data.data);
      if (attRes.data?.success) setAttendanceChart(attRes.data.data);
      if (perfRes.data?.success) setPerformanceChart(perfRes.data.data);
    } catch { /* */ }
    finally { setLoading(false); }
  }, []);

  useEffect(() => {
    academicYearAPI.getList().then(r => {
      const years = r.data?.data ?? [];
      setAcademicYears(years);
      const active = years.find((y: any) => y.is_active);
      if (active) { setSelectedYear(String(active.id)); fetchData(String(active.id)); }
      else fetchData();
    }).catch(() => fetchData());
  }, []);

  const fetchTrend = async () => {
    if (!studentId) return;
    try {
      const res = await dashboardAPI.getStudentPerformanceTrend(Number(studentId), selectedYear ? { academic_year_id: selectedYear } : undefined);
      setTrend(res.data?.data ?? null);
    } catch { setTrend(null); }
  };

  if (loading) return <div className="text-center py-12 text-gray-500">Loading dashboard...</div>;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
        <select value={selectedYear} onChange={e => { setSelectedYear(e.target.value); fetchData(e.target.value); }}
          className="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
          <option value="">All Academic Years</option>
          {academicYears.map((y: any) => <option key={y.id} value={y.id}>{y.name}{y.is_active ? ' (Active)' : ''}</option>)}
        </select>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard title="Total Students" value={stats.students} color="blue" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a4 4 0 00-4 4v1a5 5 0 00-4.472 3.341A5.972 5.972 0 005 12a1 1 0 100 2h10a1 1 0 100-2c0-1.657-.665-3.143-1.528-3.659A5 5 0 0014 5V4a4 4 0 00-4-4z"/></svg>} />
        <StatCard title="Total Teachers" value={stats.teachers} color="green" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 8a3 3 0 100-6 3 3 0 000 6zm3 7a4 4 0 01-8 0 4 4 0 00-8 0v1a2 2 0 00.293 1.707l-1.414 1.414A2 2 0 004 18h12a2 2 0 001.293-.707l-1.414-1.414A2 2 0 0018 17v-1z"/></svg>} />
        <StatCard title="Total Classes" value={stats.classes} color="yellow" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M4 4a2 2 0 00-2 2v1a1 1 0 000 2h1a1 1 0 011 1v2.586l-.293.293a1 1 0 01-1.414 0L3 10.586V5a2 2 0 012-2h8a2 2 0 012 2v5.586l-.293.293a1 1 0 01-1.414 0L11 13.586V9a1 1 0 011-1h1a1 1 0 000-2h1a2 2 0 012 2v1a1 1 0 000 2h-1a1 1 0 01-1 1h-1a1 1 0 00-1-1V6a2 2 0 00-2-2H4z" clipRule="evenodd" /></svg>} />
        <StatCard title="Attendance Rate" value={stats.attendanceRate !== null ? `${stats.attendanceRate}%` : 'N/A'} color="purple" icon={<svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/></svg>} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">Attendance Trend (7 days)</h3>
          {attendanceChart ? (
            <div className="overflow-x-auto">
              <StackedBarChart labels={attendanceChart.labels} present={attendanceChart.present} absent={attendanceChart.absent} sick={attendanceChart.sick} height={220} />
              <div className="flex justify-center gap-4 mt-3 text-xs text-gray-500">
                <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-green-500 inline-block" /> Present</span>
                <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-red-500 inline-block" /> Absent</span>
                <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-amber-500 inline-block" /> Sick/Leave</span>
              </div>
            </div>
          ) : <div className="flex h-48 items-center justify-center text-gray-400">No attendance data</div>}
        </div>

        <div className="bg-white rounded-lg shadow border p-6">
          <h3 className="text-lg font-semibold mb-4">Average Score by Subject</h3>
          {performanceChart && performanceChart.labels.length > 0 ? (
            <div className="overflow-x-auto"><BarChart data={performanceChart.data} labels={performanceChart.labels} color="#6366f1" height={220} /></div>
          ) : <div className="flex h-48 items-center justify-center text-gray-400">No grade data</div>}
        </div>
      </div>

      <div className="bg-white rounded-lg shadow border p-6">
        <h3 className="text-lg font-semibold mb-4">Student Performance Trend</h3>
        <div className="flex items-end gap-3 mb-4">
          <div className="flex-1 max-w-xs">
            <label className="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
            <input type="number" value={studentId} onChange={e => setStudentId(e.target.value)} placeholder="Enter student ID"
              className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none" />
          </div>
          <button onClick={fetchTrend} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Load Trend</button>
        </div>
        {trend && trend.labels?.length > 0 && (
          <div className="overflow-x-auto">
            <LineChart labels={trend.labels} datasets={trend.datasets} height={250} />
            <div className="flex flex-wrap justify-center gap-3 mt-3 text-xs text-gray-500">
              {Object.keys(trend.datasets).map((key, i) => (
                <span key={key} className="flex items-center gap-1">
                  <span className={`w-3 h-3 rounded inline-block`} style={{ backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'][i % 6] }} />
                  {key}
                </span>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

/* ── Parent Dashboard ── */
function ParentDashboard() {
  const [children, setChildren] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedChild, setSelectedChild] = useState<any | null>(null);
  const [grades, setGrades] = useState<any[]>([]);

  useEffect(() => {
    (async () => {
      try {
        const res = await parentAPI.getChildren();
        const data = res.data?.data ?? [];
        setChildren(data);
        if (data.length > 0) setSelectedChild(data[0]);
      } catch { /* */ }
      finally { setLoading(false); }
    })();
  }, []);

  useEffect(() => {
    if (!selectedChild) return;
    (async () => {
      try {
        const res = await parentAPI.getStudentGrades(selectedChild.id);
        const data = res.data?.data;
        if (data) setGrades(data.grades ?? []);
      } catch { /* */ }
    })();
  }, [selectedChild]);

  if (loading) return <div className="text-center py-12 text-gray-500">Loading...</div>;

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Parent Dashboard</h1>
      {children.length === 0 ? (
        <p className="text-gray-500">No children linked to your account.</p>
      ) : (
        <>
          <div className="flex gap-2">
            {children.map(c => (
              <button key={c.id} onClick={() => setSelectedChild(c)}
                className={`px-4 py-2 rounded-md text-sm font-medium ${selectedChild?.id === c.id ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'}`}>
                {c.name} ({c.kelas?.name || 'No class'})
              </button>
            ))}
          </div>

          {selectedChild && (
            <div className="bg-white rounded-lg shadow border p-6">
              <h2 className="text-lg font-semibold mb-4">{selectedChild.name} — Grades</h2>
              {grades.length === 0 ? (
                <p className="text-gray-500">No grades recorded.</p>
              ) : (
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {grades.map((g: any, i: number) => (
                      <tr key={i}>
                        <td className="px-4 py-3 text-sm">{g.subject?.name || '—'}</td>
                        <td className="px-4 py-3 text-sm">{g.score}</td>
                        <td className="px-4 py-3 text-sm font-semibold">{g.grade || '—'}</td>
                        <td className="px-4 py-3 text-sm text-gray-500">{g.term}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          )}
        </>
      )}
    </div>
  );
}

/* ── Main ── */
export default function DashboardPage() {
  const { user } = useAuth();

  return (
    <MainLayout>
      <Header />
      {!user ? (
        <div className="text-center py-12 text-gray-500">Loading...</div>
      ) : user.role === 'student' ? (
        <StudentDashboard />
      ) : user.role === 'teacher' ? (
        <TeacherDashboard />
      ) : user.role === 'parent' ? (
        <ParentDashboard />
      ) : (
        <AdminDashboard />
      )}
    </MainLayout>
  );
}
