"use client";
import React, { useEffect, useState } from 'react';
import { eventAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const DAYS = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const TYPE_COLORS: Record<string, string> = { academic: '#3b82f6', holiday: '#ef4444', exam: '#f59e0b', meeting: '#8b5cf6', extracurricular: '#10b981', other: '#6b7280' };

export default function CalendarPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [events, setEvents] = useState<any[]>([]);
  const [date, setDate] = useState(new Date());
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editEvent, setEditEvent] = useState<any>(null);
  const [form, setForm] = useState({ title: '', description: '', start_date: '', end_date: '', start_time: '', end_time: '', location: '', color: '#3b82f6', type: 'other' });

  const year = date.getFullYear();
  const month = date.getMonth();

  const fetchEvents = async () => {
    try { setLoading(true); const res = await eventAPI.getAll({ month: month + 1, year }); setEvents(res.data?.data ?? []); } finally { setLoading(false); }
  };
  useEffect(() => { fetchEvents(); }, [month, year]);

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  const getEventsForDay = (day: number) => events.filter(e => {
    const s = new Date(e.start_date);
    const end = e.end_date ? new Date(e.end_date) : s;
    const d = new Date(year, month, day);
    return d >= s && d <= end;
  });

  const today = new Date();

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { ...form };
      if (!data.end_date) delete data.end_date;
      if (!data.start_time) data.start_time = null;
      if (!data.end_time) data.end_time = null;
      if (editEvent) { await eventAPI.update(editEvent.id, data); toast('Updated', 'success'); }
      else { await eventAPI.create(data); toast('Created', 'success'); }
      setShowForm(false); setEditEvent(null); setForm({ title: '', description: '', start_date: '', end_date: '', start_time: '', end_time: '', location: '', color: '#3b82f6', type: 'other' }); fetchEvents();
    } catch { toast('Failed', 'error'); }
  };

  const handleDelete = async (id: number) => { try { await eventAPI.delete(id); toast('Deleted', 'success'); fetchEvents(); } catch { toast('Failed', 'error'); } };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Event Calendar</h1>
            {user?.role !== 'student' && <button onClick={() => { setEditEvent(null); setForm({ title: '', description: '', start_date: '', end_date: '', start_time: '', end_time: '', location: '', color: '#3b82f6', type: 'other' }); setShowForm(true); }} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Add Event</button>}
          </div>

          <div className="flex items-center justify-between">
            <button onClick={() => setDate(new Date(year, month - 1))} className="px-3 py-1.5 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">&larr; Prev</button>
            <span className="font-semibold dark:text-white">{MONTHS[month]} {year}</span>
            <button onClick={() => setDate(new Date(year, month + 1))} className="px-3 py-1.5 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Next &rarr;</button>
          </div>

          <div className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700">
            <div className="grid grid-cols-7 text-center text-xs font-medium text-gray-500 dark:text-gray-400 border-b dark:border-slate-700">
              {DAYS.map(d => <div key={d} className="py-3">{d}</div>)}
            </div>
            <div className="grid grid-cols-7">
              {Array.from({ length: firstDay }).map((_, i) => <div key={`empty-${i}`} className="min-h-[90px] p-1 border-b border-r dark:border-slate-700" />)}
              {Array.from({ length: daysInMonth }).map((_, i) => {
                const day = i + 1;
                const dayEvents = getEventsForDay(day);
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
                return (
                  <div key={day} className={`min-h-[90px] p-1 border-b border-r dark:border-slate-700 ${isToday ? 'bg-blue-50 dark:bg-blue-900/20' : ''}`}>
                    <span className={`inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded-full ${isToday ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300'}`}>{day}</span>
                    <div className="mt-1 space-y-0.5">
                      {dayEvents.slice(0, 3).map(e => (
                        <div key={e.id} className="text-[10px] px-1 py-0.5 rounded truncate text-white font-medium" style={{ backgroundColor: e.color || TYPE_COLORS[e.type] || '#6b7280' }}>
                          {e.title}
                        </div>
                      ))}
                      {dayEvents.length > 3 && <span className="text-[10px] text-gray-400">+{dayEvents.length - 3} more</span>}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Events List */}
          <div className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
            <h3 className="font-semibold mb-3 dark:text-white">All Events ({events.length})</h3>
            <div className="space-y-2">
              {events.length === 0 ? <p className="text-sm text-gray-400">No events</p> :
                events.map(e => (
                  <div key={e.id} className="flex items-center gap-3 text-sm border-b dark:border-slate-700 pb-2">
                    <div className="w-3 h-3 rounded-full flex-shrink-0" style={{ backgroundColor: e.color || TYPE_COLORS[e.type] }} />
                    <div className="flex-1">
                      <span className="font-medium text-gray-800 dark:text-gray-200">{e.title}</span>
                      <span className="text-xs text-gray-400 ml-2">{e.start_date}{e.end_date && e.end_date !== e.start_date ? ` — ${e.end_date}` : ''}</span>
                      {e.location && <span className="text-xs text-gray-400 ml-2">📍 {e.location}</span>}
                    </div>
                    <span className="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500">{e.type}</span>
                    {user?.role !== 'student' && <button onClick={() => handleDelete(e.id)} className="text-red-500 text-xs">Delete</button>}
                  </div>
                ))
              }
            </div>
          </div>
        </div>

        {/* Form Modal */}
        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{editEvent ? 'Edit' : 'Add'} Event</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Title" required className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <input type="date" value={form.start_date} onChange={e => setForm(p => ({ ...p, start_date: e.target.value }))} required className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <input type="date" value={form.end_date} onChange={e => setForm(p => ({ ...p, end_date: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="time" value={form.start_time} onChange={e => setForm(p => ({ ...p, start_time: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <input type="time" value={form.end_time} onChange={e => setForm(p => ({ ...p, end_time: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={form.location} onChange={e => setForm(p => ({ ...p, location: e.target.value }))} placeholder="Location" className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <select value={form.type} onChange={e => setForm(p => ({ ...p, type: e.target.value }))} className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    <option value="academic">Academic</option><option value="holiday">Holiday</option><option value="exam">Exam</option><option value="meeting">Meeting</option><option value="extracurricular">Extracurricular</option><option value="other">Other</option>
                  </select>
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">{editEvent ? 'Update' : 'Create'}</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
