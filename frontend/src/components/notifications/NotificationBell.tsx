"use client";
import React, { useEffect, useRef, useState } from 'react';
import { notificationAPI } from '@/lib/api';
import { motion, AnimatePresence } from 'framer-motion';

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
    try { await notificationAPI.markAsRead(id); fetch(); } catch { /* */ }
  };

  const handleMarkAllAsRead = async () => {
    try { await notificationAPI.markAllAsRead(); setCount(0); setNotifications([]); } catch { /* */ }
  };

  return (
    <div ref={ref} className="relative">
      <button onClick={() => setOpen(!open)}
        className="p-1.5 rounded-md text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-all duration-200 relative"
      >
        <svg className="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
          <path strokeLinecap="round" strokeLinejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        {count > 0 && (
          <span className="absolute -top-0.5 -right-0.5 flex size-4">
            <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-destructive/40 opacity-75" />
            <span className="relative inline-flex size-4 rounded-full bg-destructive text-[9px] font-bold text-destructive-foreground items-center justify-center">{count > 9 ? '9+' : count}</span>
          </span>
        )}
      </button>

      <AnimatePresence>
        {open && (
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: -4 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: -4 }}
            transition={{ duration: 0.15 }}
            className="absolute right-0 mt-2 w-80 rounded-xl border bg-card text-card-foreground shadow-lg z-50 overflow-hidden"
          >
            <div className="flex items-center justify-between px-4 py-3 border-b border-border">
              <h3 className="text-sm font-semibold">Notifikasi</h3>
              {count > 0 && (
                <button onClick={handleMarkAllAsRead} className="text-xs font-medium text-primary hover:text-primary/80 transition-colors">
                   Tandai sudah dibaca
                </button>
              )}
            </div>
            <div className="max-h-64 overflow-y-auto">
              {notifications.length === 0 ? (
                <p className="px-4 py-8 text-sm text-muted-foreground text-center">Tidak ada notifikasi baru</p>
              ) : (
                notifications.map(n => (
                  <button key={n.id} onClick={() => handleMarkAsRead(n.id)}
                    className="w-full text-left px-4 py-3 hover:bg-muted/50 border-b border-border last:border-b-0 transition-colors"
                  >
                    <p className="text-sm text-foreground">{n.data?.message || 'Notifikasi'}</p>
                    <p className="text-xs text-muted-foreground mt-1">{new Date(n.created_at).toLocaleString('id-ID')}</p>
                  </button>
                ))
              )}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
