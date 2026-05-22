"use client";
import React from 'react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { useRouter } from 'next/navigation';

interface MainLayoutProps {
  children: React.ReactNode;
  className?: string;
}

const navItems = [
  { label: 'Dashboard', href: '/dashboard', roles: ['admin', 'teacher', 'student'] },
  { label: 'Profile', href: '/profile', roles: ['admin', 'teacher', 'student'] },
  { label: 'Students', href: '/students', roles: ['admin', 'teacher'] },
  { label: 'Teachers', href: '/teachers', roles: ['admin'] },
  { label: 'Classes', href: '/classes', roles: ['admin', 'teacher'] },
  { label: 'Subjects', href: '/subjects', roles: ['admin', 'teacher'] },
  { label: 'Schedules', href: '/schedules', roles: ['admin', 'teacher'] },
  { label: 'Assignments', href: '/assignments', roles: ['admin', 'teacher', 'student'] },
  { label: 'Quizzes', href: '/quizzes', roles: ['admin', 'teacher', 'student'] },
  { label: 'Exam Schedules', href: '/exam-schedules', roles: ['admin', 'teacher', 'student'] },
  { label: 'Library', href: '/library', roles: ['admin', 'teacher', 'student'] },
  { label: 'Fees', href: '/fees', roles: ['admin', 'teacher', 'student'] },
  { label: 'Extracurricular', href: '/extracurriculars', roles: ['admin', 'teacher', 'student'] },
  { label: 'Messages', href: '/messages', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Announcements', href: '/announcements', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Timetable', href: '/timetable', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Attendance', href: '/attendance', roles: ['admin', 'teacher'] },
  { label: 'Grades', href: '/grades', roles: ['admin', 'teacher'] },
  { label: 'Settings', href: '/settings', roles: ['admin', 'teacher', 'student'] },
  { label: 'Reports', href: '/reports', roles: ['admin', 'teacher', 'parent'] },
  { label: 'Transcript', href: '/transcript', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Calendar', href: '/calendar', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Health', href: '/health', roles: ['admin', 'teacher', 'student', 'parent'] },
  { label: 'Import', href: '/import', roles: ['admin'] },
  { label: 'Academic Years', href: '/academic-years', roles: ['admin'] },
];

export const MainLayout = ({ children, className = '' }: MainLayoutProps) => {
  const { user, isAdmin, logout } = useAuth();
  const router = useRouter();

  const handleLogout = async () => {
    await logout();
    router.push('/login');
  };

  const visibleNav = navItems.filter(
    (item) => user?.role && item.roles.includes(user.role),
  );

  return (
    <div className={`flex min-h-screen bg-gray-50 dark:bg-slate-900 ${className}`}>
      {/* Sidebar */}
      <aside className="w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col">
        <div className="p-4 border-b border-gray-200 dark:border-slate-700">
          <h2 className="text-xl font-bold text-gray-800 dark:text-white">School Management</h2>
          {user && (
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1 capitalize">
              {user.role} — {user.name}
            </p>
          )}
        </div>

        <nav className="flex-1 p-4 space-y-1">
          {visibleNav.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
              {item.label}
            </Link>
          ))}
        </nav>

        <div className="p-4 border-t border-gray-200 dark:border-slate-700">
          <button
            onClick={handleLogout}
            className="w-full flex items-center px-3 py-2 rounded-md text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
          >
            Sign Out
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <div className="flex-1 flex flex-col">
        <div className="flex-1 p-6 overflow-auto">{children}</div>
      </div>
    </div>
  );
};
