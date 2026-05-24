"use client";
import React, { useState, useRef, useEffect, useMemo } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useAuth } from '@/contexts/AuthContext';
import { useDarkMode } from '@/contexts/DarkModeContext';
import { NotificationBell } from '@/components/notifications/NotificationBell';
import { motion, AnimatePresence } from 'framer-motion';

interface NavItem {
  label: string;
  href: string;
  roles: string[];
  icon: React.ReactNode;
}

const iconCls = 'size-[18px] shrink-0';

const navItems: NavItem[] = [
  { label: 'Dashboard', href: '/dashboard', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg> },
  { label: 'Siswa', href: '/students', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg> },
  { label: 'Guru', href: '/teachers', roles: ['admin'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg> },
  { label: 'Kelas', href: '/classes', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg> },
  { label: 'Mapel', href: '/subjects', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg> },
  { label: 'Absensi', href: '/attendance', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg> },
  { label: 'Nilai', href: '/grades', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z" /></svg> },
  { label: 'Jadwal', href: '/schedules', roles: ['admin', 'teacher'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg> },
  { label: 'Tugas', href: '/assignments', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.044 4.01 8.2 4.973 8.2 6.108V8.25m0 0H4.5M8.2 8.25h11.3m-11.3 0v9.75c0 .621.504 1.125 1.125 1.125h7.5a1.125 1.125 0 0 0 1.125-1.125V8.25" /></svg> },
  { label: 'Kuis', href: '/quizzes', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z" /></svg> },
  { label: 'Pembayaran', href: '/fees', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125V9M7.5 12h3m-3 2.25h6m-9 .75V12m0 3h3.75M3 12h3m15 0h3m-3 0v3m0-3h-1.5m1.5 0v-3m0 3H18" /></svg> },
  { label: 'Perpustakaan', href: '/library', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg> },
  { label: 'Pesan', href: '/messages', roles: ['admin', 'teacher', 'student', 'parent'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg> },
  { label: 'Pengaturan', href: '/settings', roles: ['admin', 'teacher', 'student'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.52 6.52 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg> },
];

const bottomItems: NavItem[] = [
  { label: 'Bantuan', href: '/support', roles: ['admin', 'teacher', 'student', 'parent'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z" /></svg> },
  { label: 'Dokumen', href: '/docs', roles: ['admin', 'teacher', 'student', 'parent'], icon: <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" /></svg> },
];

const sectionLabels: Record<string, string> = {
  dashboard: 'Dashboard',
  students: 'Manajemen Siswa',
  teachers: 'Manajemen Guru',
  classes: 'Manajemen Kelas',
  subjects: 'Mata Pelajaran',
  attendance: 'Absensi',
  grades: 'Penilaian',
  schedules: 'Jadwal',
  assignments: 'Tugas',
  quizzes: 'Kuis',
  fees: 'Pembayaran',
  library: 'Perpustakaan',
  messages: 'Pesan',
  settings: 'Pengaturan',
};

export const MainLayout = ({ children }: { children: React.ReactNode }) => {
  const { user, logout } = useAuth();
  const { dark, toggle: toggleDark } = useDarkMode();
  const router = useRouter();
  const pathname = usePathname();
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const searchRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (sidebarOpen && searchRef.current) searchRef.current.focus();
  }, [sidebarOpen]);

  useEffect(() => {
    const handleKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setSidebarOpen(false);
    };
    window.addEventListener('keydown', handleKey);
    return () => window.removeEventListener('keydown', handleKey);
  }, []);

  const handleLogout = async () => {
    await logout();
    router.push('/login');
  };

  const currentSection = pathname.split('/')[1] || 'dashboard';
  const sectionKey = currentSection === 'dashboard' ? 'dashboard' : currentSection;
  const sectionColors: Record<string, string> = {
    dashboard: 'var(--section-dashboard)',
    students: 'var(--section-students)',
    teachers: 'var(--section-teachers)',
    classes: 'var(--section-classes)',
    subjects: 'var(--section-subjects)',
    attendance: 'var(--section-attendance)',
    grades: 'var(--section-grades)',
    schedules: 'var(--section-schedules)',
  };
  const sectionColor = sectionColors[sectionKey] || 'var(--primary)';

  const visibleNav = useMemo(
    () => navItems.filter(item => user?.role && item.roles.includes(user.role)),
    [user]
  );
  const initials = user?.name?.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
  const userName = user?.name || 'User';
  const userRole = user?.role || '';

  const filteredNav = useMemo(() => {
    if (!searchQuery) return visibleNav;
    const q = searchQuery.toLowerCase();
    return visibleNav.filter(i => i.label.toLowerCase().includes(q));
  }, [searchQuery, visibleNav]);

  return (
    <div className="flex min-h-screen bg-background">
      {/* Mobile overlay */}
      <AnimatePresence>
        {sidebarOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.12 }}
            className="fixed inset-0 bg-black z-40 lg:hidden"
            onClick={() => setSidebarOpen(false)}
          />
        )}
      </AnimatePresence>

      {/* Sidebar */}
      <aside
        className={`
          fixed lg:sticky top-0 z-50 h-screen
          bg-sidebar border-r border-sidebar-border
          flex flex-col transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]
          ${sidebarCollapsed ? 'w-[64px]' : 'w-60'}
          ${sidebarOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full lg:translate-x-0 lg:shadow-sidebar'}
        `}
      >
        {/* Logo */}
        <div className={`flex h-16 items-center border-b border-sidebar-border ${sidebarCollapsed ? 'justify-center px-0' : 'px-4 gap-3'}`}>
          <div className="flex size-9 items-center justify-center rounded-xl bg-blue-600 shrink-0 shadow-sm">
            <svg className="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
              <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
            </svg>
          </div>
          {!sidebarCollapsed && (
            <div className="flex flex-col min-w-0">
              <span className="text-sm font-bold tracking-tight text-sidebar-foreground truncate">SMK Nusantara</span>
              <span className="text-[10px] text-sidebar-foreground/60 tracking-wider uppercase">School Management</span>
            </div>
          )}
        </div>

        {/* Search */}
        {!sidebarCollapsed && (
          <div className="px-3 pt-3">
            <div className="relative">
              <svg className="absolute left-2.5 top-1/2 -translate-y-1/2 size-3.5 text-sidebar-foreground/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
              </svg>
              <input
                ref={searchRef}
                type="text"
                value={searchQuery}
                onChange={e => setSearchQuery(e.target.value)}
                placeholder="Cari menu..."
                className="w-full h-9 rounded-lg bg-sidebar-accent border-none pl-9 pr-8 text-xs text-sidebar-foreground placeholder:text-sidebar-foreground/40 outline-none ring-1 ring-inset ring-sidebar-border focus:ring-2 focus:ring-sidebar-primary/50 transition-all"
              />
              {searchQuery && (
                <button onClick={() => setSearchQuery('')} className="absolute right-2.5 top-1/2 -translate-y-1/2 text-sidebar-foreground/40 hover:text-sidebar-foreground transition-colors">
                  <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
              )}
            </div>
          </div>
        )}

        {/* Nav */}
        <nav className="flex-1 overflow-y-auto overflow-x-hidden py-2 space-y-0.5">
          {(searchQuery ? filteredNav : visibleNav).map((item) => {
            const isActive = pathname === item.href || pathname.startsWith(item.href + '/');
            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => { setSidebarOpen(false); setSearchQuery(''); }}
                className={`
                  relative flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-all duration-150
                  ${sidebarCollapsed
                    ? 'justify-center mx-2 rounded-lg'
                    : 'mx-3 rounded-lg'
                  }
                  ${isActive
                    ? 'text-blue-400 bg-blue-500/10 border-l-[3px] border-blue-500'
                    : 'text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-sidebar-accent border-l-[3px] border-transparent'
                  }
                `}
                style={isActive ? { paddingLeft: sidebarCollapsed ? undefined : 'calc(0.75rem - 3px)' } : {}}
                title={sidebarCollapsed ? item.label : undefined}
              >
                <span className={`shrink-0 ${isActive ? 'text-blue-400' : ''}`}>{item.icon}</span>
                {!sidebarCollapsed && (
                  <span className="truncate">{item.label}</span>
                )}
              </Link>
            );
          })}
          {searchQuery && filteredNav.length === 0 && (
            <div className="px-3 py-6 text-xs text-sidebar-foreground/40 text-center">
              Tidak ada hasil
            </div>
          )}
        </nav>

        {/* Bottom items */}
        <div className="py-2 space-y-0.5 border-t border-sidebar-border">
          {bottomItems.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link key={item.href} href={item.href}
                className={`flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] ${sidebarCollapsed ? 'justify-center mx-2 rounded-lg' : 'mx-3 rounded-lg'} text-sidebar-foreground/50 hover:text-sidebar-foreground hover:bg-sidebar-accent ${isActive ? 'bg-blue-500/10 text-blue-400 border-blue-500' : 'border-transparent'}`}
                title={sidebarCollapsed ? item.label : undefined}
              >
                <span className={`shrink-0 ${isActive ? 'text-blue-400' : ''}`}>{item.icon}</span>
                {!sidebarCollapsed && <span className="truncate">{item.label}</span>}
              </Link>
            );
          })}
          <button onClick={handleLogout}
            className={`w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent ${sidebarCollapsed ? 'justify-center mx-2 rounded-lg' : 'mx-3 rounded-lg'} text-sidebar-foreground/50 hover:text-red-400 hover:bg-red-500/10`}
            title={sidebarCollapsed ? 'Keluar' : undefined}
          >
            <svg className={iconCls} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
              <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
            {!sidebarCollapsed && <span className="truncate">Keluar</span>}
          </button>
        </div>

        {/* User profile */}
        <div className={`border-t border-sidebar-border py-3 ${sidebarCollapsed ? 'flex justify-center px-0' : 'px-3'}`}>
          <div className={`flex items-center gap-3 ${sidebarCollapsed ? 'flex-col' : ''}`}>
            <div className="flex size-9 items-center justify-center rounded-lg text-xs font-bold text-white shrink-0 shadow-sm"
              style={{ background: '#6366f1' }}
            >
              {initials}
            </div>
            {!sidebarCollapsed && (
              <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold text-sidebar-foreground truncate leading-tight">{userName}</p>
                <p className="text-[11px] text-sidebar-foreground/60 capitalize truncate">{userRole}</p>
              </div>
            )}
          </div>
        </div>
      </aside>

      {/* Main content */}
      <div className="flex-1 flex flex-col min-w-0">
        <header className="sticky top-0 z-30 flex h-16 items-center gap-2 border-b border-border bg-card px-4 sm:px-6 shadow-sm">
          <button
            onClick={() => setSidebarOpen(true)}
            className="lg:hidden p-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
            aria-label="Buka menu"
          >
            <svg className="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
              <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>

          <button
            onClick={() => setSidebarCollapsed(!sidebarCollapsed)}
            className="hidden lg:flex p-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
            title={sidebarCollapsed ? 'Perluas sidebar' : 'Persempit sidebar'}
          >
            <svg className="size-5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"
              style={{ transform: sidebarCollapsed ? 'rotate(180deg)' : 'none' }}
            >
              <path strokeLinecap="round" strokeLinejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>
          </button>

          {/* Section accent indicator */}
          <div className="w-1 h-6 rounded-full shrink-0 hidden sm:block" style={{ backgroundColor: sectionColor }} />

          {/* Breadcrumb */}
          <nav className="hidden sm:flex items-center gap-1.5 text-xs text-muted-foreground min-w-0">
            <Link href="/dashboard" className="hover:text-foreground transition-colors shrink-0 font-medium">Dashboard</Link>
            {pathname !== '/dashboard' && pathname.split('/').filter(Boolean).map((segment, i, arr) => (
              <React.Fragment key={segment}>
                <svg className="size-3 text-muted-foreground/20 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                {i === arr.length - 1 ? (
                  <span className="text-foreground font-semibold truncate max-w-[140px]">
                    {sectionLabels[segment] || segment.charAt(0).toUpperCase() + segment.slice(1)}
                  </span>
                ) : (
                  <Link href={`/${arr.slice(0, i + 1).join('/')}`} className="hover:text-foreground transition-colors shrink-0">
                    {segment.charAt(0).toUpperCase() + segment.slice(1)}
                  </Link>
                )}
              </React.Fragment>
            ))}
          </nav>

          {/* Section label (mobile) */}
          <span className="sm:hidden text-sm font-semibold text-foreground truncate">
            {sectionLabels[sectionKey] || 'Dashboard'}
          </span>

          <div className="flex-1" />

          {/* Search bar */}
          <div className="relative hidden sm:flex items-center gap-2 rounded-lg border border-border bg-muted/60 px-3 h-9 text-sm text-muted-foreground w-44 lg:w-56 transition-all duration-200 focus-within:border-primary/40 focus-within:bg-card focus-within:shadow-xs">
            <svg className="size-3.5 shrink-0 text-muted-foreground/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
              <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
              type="text"
              placeholder="Cari..."
              className="bg-transparent border-none outline-none w-full text-xs text-foreground placeholder:text-muted-foreground/40"
            />
            <kbd className="hidden lg:inline-flex items-center gap-0.5 rounded border border-border bg-background px-1.5 py-0.5 text-[10px] text-muted-foreground/50 font-mono">
              <span>⌘</span>K
            </kbd>
          </div>

          <button className="sm:hidden p-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
            <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
              <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>

          {/* Dark mode toggle */}
          <button onClick={toggleDark}
            className="p-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-all duration-200"
            title={dark ? 'Mode Terang' : 'Mode Gelap'}
          >
            <motion.div key={dark ? 'dark' : 'light'} initial={{ rotate: -90, opacity: 0 }} animate={{ rotate: 0, opacity: 1 }} transition={{ duration: 0.15 }}>
              {dark ? (
                <svg className="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              ) : (
                <svg className="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
              )}
            </motion.div>
          </button>

          <NotificationBell />
        </header>

        <main className="flex-1 p-5 sm:p-7 lg:p-9">
          <motion.div
            key={pathname}
            initial={{ opacity: 0, y: 8 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.2, ease: 'easeOut' }}
          >
            {children}
          </motion.div>
        </main>
      </div>
    </div>
  );
};
