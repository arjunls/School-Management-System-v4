"use client";

import React from 'react';
import { useRouter } from 'next/navigation';
import { authAPI } from '@/lib/api';
import { NotificationBell } from '@/components/notifications/NotificationBell';
import { LanguageSwitcher } from '@/components/ui/LanguageSwitcher';
import { DarkModeToggle } from '@/components/ui/DarkModeToggle';

interface HeaderProps {
  className?: string;
}

export const Header = ({ className = '' }: HeaderProps) => {
  const router = useRouter();

  const handleSignOut = async () => {
    await authAPI.logout();
    router.push('/login');
  };

  return (
    <header className={`flex items-center justify-between px-6 py-4 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 ${className}`}>
      <div className="flex items-center space-x-4">
        <div className="flex items-center space-x-3">
          <div className="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
            SM
          </div>
          <div>
            <h1 className="text-lg font-semibold text-gray-900 dark:text-white">School Management System</h1>
            <p className="text-sm text-gray-500 dark:text-gray-400">Education Excellence Platform</p>
          </div>
        </div>
      </div>
      
      <div className="flex items-center space-x-4">
        <LanguageSwitcher />
        <DarkModeToggle />
        <NotificationBell />
        <div className="relative">
          <button 
            onClick={handleSignOut}
            className="flex items-center px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l2-2m0 0l-2-2m2 2l-2 2m2 2l-2 2m0 0l2-2m-2 2l-2-2" />
            </svg>
            Sign Out
          </button>
        </div>
      </div>
    </header>
  );
};