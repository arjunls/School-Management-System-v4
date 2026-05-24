"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { dashboardAPI, gradeAPI, attendanceAPI, scheduleAPI, studentAPI, classAPI, teacherAPI, subjectAPI, parentAPI, academicYearAPI, feeAPI, assignmentAPI, libraryAPI } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { MainLayout } from '@/components/layout/MainLayout';
import { StatCard } from '@/components/widgets/StatCard';
import { motion } from 'framer-motion';
import { Badge, Card, CardHeader, Skeleton, EmptyState } from '@/components/ui';
import Link from 'next/link';

interface AttendanceChart { labels: string[]; present: number[]; absent: number[]; sick: number[]; }
interface PerformanceChart { labels: string[]; data: number[]; }

const stagger = { animate: { transition: { staggerChildren: 0.04 } } };
const fadeUp = { initial: { opacity: 0, y: 10 }, animate: { opacity: 1, y: 0 } };

const roleColors: Record<string, { accent: string; bg: string }> = {
  admin: { accent: '#4f46e5', bg: '#4338ca' },
  teacher: { accent: '#7c3aed', bg: '#6d28d9' },
  student: { accent: '#0d9488', bg: '#0f766e' },
  parent: { accent: '#ea580c', bg: '#c2410c' },
};

const sectionLinks = [
  { label: 'Siswa', href: '/students', icon: <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>, roles: ['admin', 'teacher'] },
  { label: 'Guru', href: '/teachers', icon: <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>, roles: ['admin'] },
  { label: 'Kelas', href: '/classes', icon: <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>, roles: ['admin', 'teacher'] },
  { label: 'Absensi', href: '/attendance', icon: <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg>, roles: ['admin', 'teacher'] },
  { label: 'Jadwal', href: '/schedules', icon: <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg>, roles: ['admin', 'teacher'] },
];

function SectionCard({ title, action, children, accent }: { title: string; action?: React.ReactNode; children: React.ReactNode; accent?: string }) {
  return (
    <motion.div variants={fadeUp}>
      <Card accent={accent} padding="none" hover>
        <CardHeader title={title} action={action} className="px-5 py-4 border-b border-border mb-0" />
        <div className="p-5">{children}</div>
      </Card>
    </motion.div>
  );
}

function WelcomeBanner({ name, role, greeting, initials }: { name: string; role: string; greeting: string; initials: string }) {
  const rc = roleColors[role] || roleColors.admin;
  return (
    <motion.div variants={fadeUp}
      className="rounded-xl p-6 sm:p-7 text-white shadow-sm"
      style={{ backgroundColor: rc.bg }}
    >
      <div className="flex items-center gap-4">
        <div className="flex size-11 sm:size-12 items-center justify-center rounded-lg bg-white/15 text-sm font-bold ring-1 ring-white/20 shrink-0">
          {initials}
        </div>
        <div className="flex-1 min-w-0">
          <p className="text-xs font-medium text-white/70 uppercase tracking-wider">{greeting}</p>
          <h1 className="text-lg sm:text-xl font-bold tracking-tight truncate mt-0.5">{name}</h1>
          <p className="text-xs text-white/70 capitalize mt-0.5">{role}</p>
        </div>
        <div className="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white/15 text-xs">
          <span className="flex size-2 rounded-full bg-emerald-300" />
          <span className="text-white/70 font-medium">Online</span>
        </div>
      </div>
    </motion.div>
  );
}

function LoadingSkeleton() {
  return (
    <div className="space-y-6">
      <Skeleton variant="card" className="h-24" />
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {[...Array(4)].map((_, i) => (
          <div key={i} className="rounded-lg border bg-card p-5">
            <div className="flex justify-between mb-3"><Skeleton variant="text" className="h-4 w-20" /><Skeleton variant="text" className="size-9" /></div>
            <Skeleton variant="text" className="h-7 w-16" />
          </div>
        ))}
      </div>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {[...Array(2)].map((_, i) => (
          <div key={i} className="rounded-lg border bg-card p-5"><Skeleton variant="text" className="h-4 w-32 mb-4" /><Skeleton variant="chart" /></div>
        ))}
      </div>
    </div>
  );
}

function StudentDashboard() {
  const { user } = useAuth();
  const [grades, setGrades] = useState<{ score?: number; subject?: { name: string } | null }[]>([]);
  const [attendance, setAttendance] = useState<{ status: string }[]>([]);
  const [schedules, setSchedules] = useState<any[]>([]);
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
            const sBody = s.data as any;
            const kelasId = sBody?.data?.kelas?.id;
            if (kelasId) {
              const sc = await scheduleAPI.getList({ class_id: kelasId });
              const scBody = sc.data as any;
              return Array.isArray(scBody?.data) ? scBody.data : [];
            }
            return [];
          })(),
          feeAPI.getInvoices({ student_id: user?.id }),
          assignmentAPI.getList({ student_id: user?.id }),
          libraryAPI.getLoans({ per_page: 5 }),
        ]);
        const ex = (r: unknown) => { const b = r as any; return Array.isArray(b?.data) ? b.data : []; };
        setGrades(ex(gRes.data));
        setAttendance(ex(aRes.data));
        setSchedules(ex(sRes));
        setInvoices(ex(invRes.data));
        setAssignments(ex(asgRes.data));
        setLoans(ex(loanRes.data?.data ?? loanRes.data));
      } catch {  } finally { setLoading(false); }
    })();
  }, [user]);

  const presentCount = attendance.filter(a => a.status === 'present').length;
  const attRate = attendance.length ? Math.round((presentCount / attendance.length) * 100) : null;
  const dueAssignments = assignments.filter(a => a.status !== 'submitted');
  const pendingInvoices = invoices.filter(i => i.status !== 'paid');

  if (loading) return <LoadingSkeleton />;

  const initials = user?.name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
  const greeting = (() => { const h = new Date().getHours(); return h < 11 ? 'Selamat Pagi' : h < 15 ? 'Selamat Siang' : h < 18 ? 'Selamat Sore' : 'Selamat Malam'; })();

  return (
    <motion.div variants={stagger} initial="initial" animate="animate" className="space-y-6">
      <WelcomeBanner name={user?.name?.split(' ')[0] || 'Siswa'} role="siswa" greeting={greeting} initials={initials} />
      <motion.div variants={fadeUp} className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <StatCard title="Nilai" value={grades.length} accent="#7c3aed"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.044 4.01 8.2 4.973 8.2 6.108V8.25m0 0H4.5M8.2 8.25h11.3m-11.3 0v9.75c0 .621.504 1.125 1.125 1.125h7.5a1.125 1.125 0 001.125-1.125V8.25" /></svg>} />
        <StatCard title="Absensi" value={attRate !== null ? `${attRate}%` : 'N/A'} accent="#0d9488"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>} />
        <StatCard title="Tagihan" value={pendingInvoices.length} accent="#d97706"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>} />
        <StatCard title="Tugas" value={dueAssignments.length} accent="#dc2626"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>} />
      </motion.div>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <SectionCard title="Nilai Saya" accent="#7c3aed">
          {grades.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Belum ada nilai.</p> : (
            <div className="overflow-x-auto -mx-5">
              <table className="w-full text-sm">
                <thead><tr className="border-b border-border"><th className="text-left px-5 py-2.5 font-semibold text-[11px] uppercase tracking-wider text-muted-foreground">Mata Pelajaran</th><th className="text-right px-5 py-2.5 font-semibold text-[11px] uppercase tracking-wider text-muted-foreground">Nilai</th></tr></thead>
                <tbody className="divide-y divide-border">
                  {grades.map((g, i) => (
                    <tr key={i} className="hover:bg-muted/50 transition-colors">
                      <td className="px-5 py-2.5 text-sm">{g.subject?.name || '—'}</td>
                      <td className="px-5 py-2.5 text-right text-sm font-semibold">{g.score ?? '—'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </SectionCard>
        <SectionCard title="Jadwal" accent="#0d9488">
          {schedules.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Tidak ada jadwal.</p> : (
            <div className="overflow-x-auto -mx-5">
              <table className="w-full text-sm">
                <thead><tr className="border-b border-border"><th className="text-left px-5 py-2.5 font-semibold text-[11px] uppercase tracking-wider text-muted-foreground">Hari</th><th className="text-left px-5 py-2.5 font-semibold text-[11px] uppercase tracking-wider text-muted-foreground">Waktu</th><th className="text-left px-5 py-2.5 font-semibold text-[11px] uppercase tracking-wider text-muted-foreground">Mapel</th></tr></thead>
                <tbody className="divide-y divide-border">
                  {schedules.slice(0, 5).map((s, i) => (
                    <tr key={i} className="hover:bg-muted/50 transition-colors">
                      <td className="px-5 py-2.5 text-sm capitalize font-medium">{s.day_of_week}</td>
                      <td className="px-5 py-2.5 text-sm text-muted-foreground">{s.start_time?.slice(0,5)}&ndash;{s.end_time?.slice(0,5)}</td>
                      <td className="px-5 py-2.5 text-sm">{s.subject?.name || '—'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </SectionCard>
        <SectionCard title="Tugas" accent="#d97706">
          {dueAssignments.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Semua selesai!</p> : (
            <div className="space-y-2">
              {dueAssignments.slice(0, 4).map((a: any) => (
                <div key={a.id} className="flex items-center justify-between p-3 rounded-lg bg-muted hover:bg-border transition-colors">
                  <div className="flex items-center gap-3">
                    <div className="size-2 rounded-full bg-amber-500" />
                    <span className="text-sm font-medium">{a.title || '—'}</span>
                  </div>
                  <span className="text-xs text-muted-foreground">{a.due_date ? new Date(a.due_date).toLocaleDateString('en-GB') : ''}</span>
                </div>
              ))}
            </div>
          )}
        </SectionCard>
        <SectionCard title="Peminjaman" accent="#2563eb">
          {loans.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Tidak ada buku dipinjam.</p> : (
            <div className="space-y-2">
              {loans.map((l: any) => (
                <div key={l.id} className="flex items-center justify-between p-3 rounded-lg bg-muted hover:bg-border transition-colors">
                  <span className="text-sm font-medium">{l.book?.title || '—'}</span>
                  <Badge variant={l.status === 'overdue' ? 'danger' : 'success'}>{l.status}</Badge>
                </div>
              ))}
            </div>
          )}
        </SectionCard>
      </div>
    </motion.div>
  );
}

function TeacherDashboard() {
  const { user } = useAuth();
  const [classes, setClasses] = useState<any[]>([]);
  const [subjects, setSubjects] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [cRes, sRes] = await Promise.all([
          classAPI.getList({ homeroom_teacher_id: user?.id }),
          subjectAPI.getList({ teacher_id: user?.id }),
        ]);
        const ex = (r: unknown) => { const b = r as any; return Array.isArray(b?.data) ? b.data : []; };
        setClasses(ex(cRes.data));
        setSubjects(ex(sRes.data));
      } catch {  } finally { setLoading(false); }
    })();
  }, [user]);

  const totalStudents = classes.reduce((sum: number, c: any) => sum + (c.students_count || 0), 0);
  if (loading) return <LoadingSkeleton />;

  const initials = user?.name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
  const greeting = (() => { const h = new Date().getHours(); return h < 11 ? 'Selamat Pagi' : h < 15 ? 'Selamat Siang' : h < 18 ? 'Selamat Sore' : 'Selamat Malam'; })();

  return (
    <motion.div variants={stagger} initial="initial" animate="animate" className="space-y-6">
      <WelcomeBanner name={user?.name || 'Guru'} role="guru" greeting={greeting} initials={initials} />
      <motion.div variants={fadeUp} className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <StatCard title="Kelas" value={classes.length} accent="#7c3aed"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>} />
        <StatCard title="Mata Pelajaran" value={subjects.length} accent="#0d9488"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>} />
        <StatCard title="Siswa" value={totalStudents} accent="#d97706"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>} />
      </motion.div>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <SectionCard title="Kelas Saya" accent="#7c3aed">
          {classes.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Belum ada kelas.</p> : (
            <div className="space-y-2">
              {classes.map((c: any) => (
                <Link key={c.id} href={`/classes/${c.id}`}
                  className="flex items-center justify-between p-3 rounded-lg bg-muted hover:bg-border transition-colors group"
                >
                  <div className="flex items-center gap-3">
                    <div className="size-8 rounded-lg bg-violet-100 dark:bg-violet-900 flex items-center justify-center text-violet-600 dark:text-violet-400">
                      <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                    </div>
                    <div>
                      <span className="font-medium text-sm">{c.name}</span>
                      <span className="text-xs text-muted-foreground ml-2">{c.students_count ?? 0} siswa</span>
                    </div>
                  </div>
                  <svg className="size-4 text-muted-foreground group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </Link>
              ))}
            </div>
          )}
        </SectionCard>
        <SectionCard title="Mata Pelajaran Saya" accent="#0d9488">
          {subjects.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Belum ada mata pelajaran.</p> : (
            <div className="space-y-2">
              {subjects.map((s: any) => (
                <div key={s.id} className="flex items-center justify-between p-3 rounded-lg bg-muted hover:bg-border transition-colors">
                  <div className="flex items-center gap-3">
                    <div className="size-2 rounded-full bg-emerald-500" />
                    <div>
                      <span className="font-medium text-sm">{s.name}</span>
                      <span className="text-xs text-muted-foreground ml-2">({s.code})</span>
                    </div>
                  </div>
                  <svg className="size-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </div>
              ))}
            </div>
          )}
        </SectionCard>
      </div>
    </motion.div>
  );
}

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
    } catch {  } finally { setLoading(false); }
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

  if (loading) return <LoadingSkeleton />;

  const initials = 'AD';
  const greeting = (() => { const h = new Date().getHours(); return h < 11 ? 'Selamat Pagi' : h < 15 ? 'Selamat Siang' : h < 18 ? 'Selamat Sore' : 'Selamat Malam'; })();

  return (
    <motion.div variants={stagger} initial="initial" animate="animate" className="space-y-6">
      <WelcomeBanner name="Administrator" role="admin" greeting={greeting} initials={initials} />

      {/* Quick actions */}
      <motion.div variants={fadeUp}>
        <div className="flex flex-wrap items-center gap-1.5">
          {sectionLinks.filter(s => s.roles.includes('admin')).map(s => (
            <Link key={s.href} href={s.href}
              className="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-2.5 py-1.5 text-xs font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all"
            >
              {s.icon}
              {s.label}
            </Link>
          ))}
          <Link href="/students/create"
            className="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-medium text-primary-foreground shadow-xs hover:opacity-90 transition-all"
          >
            <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Siswa
          </Link>
        </div>
      </motion.div>

      {/* Stats */}
      <motion.div variants={fadeUp} className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard title="Siswa" value={stats.students} trend={{ value: '12.5%', isPositive: true }} accent="#2563eb"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>} />
        <StatCard title="Guru" value={stats.teachers} trend={{ value: '8.2%', isPositive: true }} accent="#7c3aed"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>} />
        <StatCard title="Kelas" value={stats.classes} trend={{ value: '2.1%', isPositive: false }} accent="#d97706"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>} />
        <StatCard title="Kehadiran" value={stats.attendanceRate !== null ? `${stats.attendanceRate}%` : 'N/A'} trend={{ value: '18.2%', isPositive: true }} accent="#0d9488"
          icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>} />
      </motion.div>

      {/* Academic year selector */}
      <motion.div variants={fadeUp}>
        <div className="flex flex-wrap items-center gap-1.5">
          <span className="text-xs font-medium text-muted-foreground mr-1">Tahun Akademik:</span>
          {academicYears.map((y: any) => (
            <button key={y.id} onClick={() => { setSelectedYear(String(y.id)); fetchData(String(y.id)); }}
              className={`px-2.5 py-1 rounded-md text-xs font-medium transition-all ${String(y.id) === selectedYear ? 'bg-primary text-primary-foreground shadow-xs' : 'border border-border bg-card hover:bg-muted hover:text-foreground text-muted-foreground'}`}
            >
              {y.name}
              {y.is_active && <span className="ml-1 text-[10px] opacity-70">(Aktif)</span>}
            </button>
          ))}
        </div>
      </motion.div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <SectionCard title="Tren Kehadiran" accent="#0d9488"
          action={<Badge variant="success">Tahun Ini</Badge>}
        >
          {attendanceChart ? (
            <StackedBarChart labels={attendanceChart.labels} present={attendanceChart.present} absent={attendanceChart.absent} sick={attendanceChart.sick} />
          ) : <div className="flex h-48 items-center justify-center text-muted-foreground text-sm">Belum ada data</div>}
        </SectionCard>
        <SectionCard title="Rata-rata Nilai per Mata Pelajaran" accent="#7c3aed"
          action={<Badge variant="info">Semester Ini</Badge>}
        >
          {performanceChart && performanceChart.labels.length > 0 ? (
            <BarChart data={performanceChart.data} labels={performanceChart.labels} color="oklch(0.55 0.19 240)" />
          ) : <div className="flex h-48 items-center justify-center text-muted-foreground text-sm">Belum ada data</div>}
        </SectionCard>
      </div>

      {/* Student trend */}
      <SectionCard title="Tren Nilai Siswa" accent="#d97706">
        <div className="flex flex-col sm:flex-row items-start sm:items-end gap-3 mb-4">
          <div className="w-full sm:max-w-xs">
            <label className="block text-xs font-medium text-muted-foreground mb-1">Cari berdasarkan ID Siswa</label>
            <input type="number" value={studentId} onChange={e => setStudentId(e.target.value)} placeholder="Masukkan ID siswa"
              className="h-9 w-full rounded-lg border border-border bg-background px-3 py-1 text-sm shadow-sm focus-visible:border-ring focus-visible:ring-ring/15 focus-visible:ring-[3px] outline-none transition-all placeholder:text-muted-foreground/50" />
          </div>
          <button onClick={fetchTrend}
            className="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-all bg-blue-600 text-white hover:bg-blue-500 h-9 px-4 py-2 shadow-sm">
            <svg className="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z" /></svg>
            Lihat Tren
          </button>
        </div>
        {trend && trend.labels?.length > 0 && (
          <LineChart labels={trend.labels} datasets={trend.datasets} />
        )}
      </SectionCard>
    </motion.div>
  );
}

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
      } catch {  } finally { setLoading(false); }
    })();
  }, []);

  useEffect(() => {
    if (!selectedChild) return;
    (async () => {
      try {
        const res = await parentAPI.getStudentGrades(selectedChild.id);
        const data = res.data?.data;
        if (data) setGrades(data.grades ?? []);
      } catch {  }
    })();
  }, [selectedChild]);

  if (loading) return <LoadingSkeleton />;

  const greeting = (() => { const h = new Date().getHours(); return h < 11 ? 'Selamat Pagi' : h < 15 ? 'Selamat Siang' : h < 18 ? 'Selamat Sore' : 'Selamat Malam'; })();

  return (
    <motion.div variants={stagger} initial="initial" animate="animate" className="space-y-6">
      <WelcomeBanner name="Orang Tua" role="parent" greeting={greeting} initials="OT" />
      {children.length === 0 ? (
        <p className="text-muted-foreground text-sm">Belum ada anak terdaftar.</p>
      ) : (
        <>
          <motion.div variants={fadeUp} className="flex flex-wrap gap-2">
            {children.map((c: any) => (
              <button key={c.id} onClick={() => setSelectedChild(c)}
                className={`inline-flex items-center gap-2 rounded-md text-sm font-medium transition-all h-9 px-4 py-2 ${selectedChild?.id === c.id ? 'bg-blue-600 text-white shadow-sm' : 'border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground'}`}
              >
                <div className="size-5 rounded-full bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white">{c.name?.charAt(0)}</div>
                {c.name} {c.kelas?.name ? `(${c.kelas.name})` : ''}
              </button>
            ))}
          </motion.div>
          {selectedChild && (
            <SectionCard title={`${selectedChild.name} — Nilai`} accent="#2563eb">
              {grades.length === 0 ? <p className="text-sm text-muted-foreground text-center py-6">Belum ada nilai.</p> : (
                <div className="overflow-x-auto -mx-5">
                  <table className="w-full text-sm">
                    <thead><tr className="border-b border-border"><th className="text-left px-5 py-2 font-semibold text-xs uppercase tracking-wider text-muted-foreground">Mata Pelajaran</th><th className="text-right px-5 py-2 font-semibold text-xs uppercase tracking-wider text-muted-foreground">Nilai</th><th className="text-right px-5 py-2 font-semibold text-xs uppercase tracking-wider text-muted-foreground">Grade</th><th className="text-right px-5 py-2 font-semibold text-xs uppercase tracking-wider text-muted-foreground">Semester</th></tr></thead>
                    <tbody className="divide-y divide-border">
                      {grades.map((g: any, i: number) => (
                        <tr key={i} className="hover:bg-muted transition-colors">
                          <td className="px-5 py-2.5">{g.subject?.name || '—'}</td>
                          <td className="px-5 py-2.5 text-right font-medium">{g.score}</td>
                          <td className="px-5 py-2.5 text-right font-semibold">{g.grade || '—'}</td>
                          <td className="px-5 py-2.5 text-right text-muted-foreground">{g.term}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </SectionCard>
          )}
        </>
      )}
    </motion.div>
  );
}

function StackedBarChart({ labels, present, absent, sick, height = 220 }: { labels: string[]; present: number[]; absent: number[]; sick: number[]; height?: number }) {
  const max = Math.max(...present.map((_, i) => present[i] + absent[i] + sick[i]), 1);
  const w = 36; const gap = 10;
  const totalW = (w + gap) * labels.length - gap;
  return (
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.5 }}>
      <svg width="100%" height={height} viewBox={`0 0 ${Math.max(totalW, 200)} ${height}`} preserveAspectRatio="xMidYMid meet">
        {labels.map((label, i) => {
          const hScale = (v: number) => (v / max) * (height - 30);
          const x = i * (w + gap);
          const baseY = height - 20;
          return (
            <g key={i}>
              <motion.rect initial={{ height: 0, y: baseY }} animate={{ height: hScale(sick[i]), y: baseY - hScale(present[i]) - hScale(absent[i]) - hScale(sick[i]) }} transition={{ duration: 0.4, delay: i * 0.03 }} x={x} width={w} rx="3" fill="#f59e0b" />
              <motion.rect initial={{ height: 0, y: baseY }} animate={{ height: hScale(absent[i]), y: baseY - hScale(present[i]) - hScale(absent[i]) }} transition={{ duration: 0.4, delay: i * 0.03 }} x={x} width={w} rx="3" fill="#ef4444" />
              <motion.rect initial={{ height: 0, y: baseY }} animate={{ height: hScale(present[i]), y: baseY - hScale(present[i]) }} transition={{ duration: 0.4, delay: i * 0.03 }} x={x} width={w} rx="3" fill="#22c55e" />
              <text x={x + w / 2} y={baseY + 12} textAnchor="middle" fontSize="9" fill="currentColor" className="fill-muted-foreground">{label}</text>
            </g>
          );
        })}
      </svg>
      <div className="flex justify-center gap-4 mt-2 text-xs text-muted-foreground">
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-sm bg-green-500 inline-block" /> Hadir</span>
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-sm bg-red-500 inline-block" /> Absen</span>
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-sm bg-amber-500 inline-block" /> Sakit</span>
      </div>
    </motion.div>
  );
}

function BarChart({ data, labels, color, height = 220 }: { data: number[]; labels: string[]; color: string; height?: number }) {
  const max = Math.max(...data, 1);
  const w = 40; const gap = 12;
  const totalW = (w + gap) * data.length - gap;
  return (
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.5 }}>
      <svg width="100%" height={height} viewBox={`0 0 ${Math.max(totalW, 200)} ${height}`} preserveAspectRatio="xMidYMid meet">
        {data.map((v, i) => {
          const barH = (v / max) * (height - 30);
          const x = i * (w + gap);
          const y = height - 20 - barH;
          return (
            <g key={i}>
              <motion.rect initial={{ height: 0, y: height - 20 }} animate={{ height: barH, y }} transition={{ duration: 0.4, delay: i * 0.05, ease: 'easeOut' }} x={x} width={w} rx="4" fill={color} />
              <text x={x + w / 2} y={height - 4} textAnchor="middle" fontSize="10" fill="currentColor" className="fill-muted-foreground">{labels[i]}</text>
              <motion.text initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.3 + i * 0.05 }} x={x + w / 2} y={y - 5} textAnchor="middle" fontSize="10" fill="currentColor" fontWeight="600">{v}</motion.text>
            </g>
          );
        })}
      </svg>
    </motion.div>
  );
}

function LineChart({ labels, datasets, height = 250 }: { labels: string[]; datasets: Record<string, number[]>; height?: number }) {
  const allValues = Object.values(datasets).flat();
  const max = Math.max(...allValues, 1);
  const min = Math.min(...allValues, 0);
  const range = max - min || 1;
  const chartColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
  const chartW = 600;
  const chartH = height - 40;
  const stepX = labels.length > 1 ? (chartW - 40) / (labels.length - 1) : 0;
  return (
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.5 }}>
      <svg width="100%" height={height} viewBox={`0 0 ${chartW} ${height}`} preserveAspectRatio="xMidYMid meet">
        {Object.entries(datasets).map(([key, data], idx) => {
          const color = chartColors[idx % chartColors.length];
          const points = data.map((v, i) => { const x = 20 + i * stepX; const y = chartH - ((v - min) / range) * (chartH - 20) + 10; return `${x},${y}`; }).join(' ');
          return (
            <g key={key}>
              <motion.polyline initial={{ pathLength: 0 }} animate={{ pathLength: 1 }} transition={{ duration: 0.8, delay: idx * 0.2, ease: 'easeInOut' }} fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" points={points} style={{ pathLength: 1 }} />
              {data.map((v, i) => (<motion.circle key={i} initial={{ scale: 0, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} transition={{ delay: 0.5 + idx * 0.2 + i * 0.03 }} cx={20 + i * stepX} cy={chartH - ((v - min) / range) * (chartH - 20) + 10} r="3" fill={color} stroke="white" strokeWidth="2" />))}
            </g>
          );
        })}
        {labels.map((l, i) => (<text key={i} x={20 + i * stepX} y={height - 5} textAnchor="middle" fontSize="9" fill="currentColor" className="fill-muted-foreground">{l}</text>))}
      </svg>
      <div className="flex flex-wrap justify-center gap-3 mt-2 text-xs text-muted-foreground">
        {Object.keys(datasets).map((key, i) => (
          <span key={key} className="flex items-center gap-1"><span className="w-3 h-3 rounded inline-block" style={{ backgroundColor: chartColors[i % chartColors.length] }} />{key}</span>
        ))}
      </div>
    </motion.div>
  );
}

export default function DashboardPage() {
  const { user } = useAuth();
  return (
    <MainLayout>
      {!user ? (
        <div className="flex items-center justify-center py-20 text-muted-foreground">Memuat...</div>
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
