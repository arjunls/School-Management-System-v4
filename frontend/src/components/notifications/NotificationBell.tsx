"use client";
import React, { useEffect, useRef, useState } from 'react';
import { notificationAPI } from '@/lib/api';

interface Notification {
  id: string;
  data: { message: string; [key: string]: any };
  read_at: string | null;
  created_at: string;
}

export function NotificationBell() {
  const [open, setOpen] = useState(false);
  const [count, setCount] = useState(0);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const ref = useRef<HTMLDivElement>(null);

  const fetch = async () => {
    try {
      const res = await notificationAPI.getUnread();
      const d = res.data?.data;
      if (d) { setCount(d.count); setNotifications(d.notifications); }
    } catch { /* */ }
  };

  useEffect(() => { fetch(); const iv = setInterval(fetch, 30000); return () => clearInterval(iv); }, []);

  useEffect(() => {
    const handler = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false); };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  const handleMarkAsRead = async (id: string) => {
    try {
      await notificationAPI.markAsRead(id);
      fetch();
    } catch { /* */ }
  };

  const handleMarkAllAsRead = async () => {
    try {
      await notificationAPI.markAllAsRead();
      setCount(0);
      setNotifications([]);
    } catch { /* */ }
  };

  return (
    <div ref={ref} className="relative">
      <button onClick={() => setOpen(!open)} className="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        {count > 0 && <span className="absolute top-0 right-0 inline-flex items-center justify-center h-4 w-4 rounded-full bg-red-500 text-white text-xs font-bold">{count > 9 ? '9+' : count}</span>}
      </button>

      {open && (
        <div className="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50">
          <div className="flex items-center justify-between px-4 py-3 border-b">
            <h3 className="text-sm font-semibold text-gray-900">Notifications</h3>
            {count > 0 && <button onClick={handleMarkAllAsRead} className="text-xs text-indigo-600 hover:text-indigo-800">Mark all as read</button>}
          </div>
          <div className="max-h-64 overflow-y-auto">
            {notifications.length === 0 ? (
              <p className="px-4 py-6 text-sm text-gray-500 text-center">No new notifications</p>
            ) : (
              notifications.map(n => (
                <div key={n.id} className="px-4 py-3 hover:bg-gray-50 border-b last:border-b-0 cursor-pointer" onClick={() => handleMarkAsRead(n.id)}>
                  <p className="text-sm text-gray-800">{n.data?.message || 'Notification'}</p>
                  <p className="text-xs text-gray-400 mt-1">{new Date(n.created_at).toLocaleString()}</p>
                </div>
              ))
            )}
          </div>
        </div>
      )}
    </div>
  );
}
