"use client";
import React, { useEffect, useState } from 'react';
import { eventAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input, Select } from '@/components/ui/Input';

const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAYS =['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
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
          <PageHeader
            title="Kalender Acara"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Kalender' }]}
            action={
              user?.role !== 'student' && (
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setEditEvent(null); setForm({ title: '', description: '', start_date: '', end_date: '', start_time: '', end_time: '', location: '', color: '#3b82f6', type: 'other' }); setShowForm(true); }}
                >
                  Tambah Acara
                </Button>
              )
            }
          />

          <div className="flex items-center justify-between">
            <Button variant="outline" size="sm" onClick={() => setDate(new Date(year, month - 1))}>&larr; Sebelumnya</Button>
            <span className="font-semibold">{MONTHS[month]} {year}</span>
            <Button variant="outline" size="sm" onClick={() => setDate(new Date(year, month + 1))}>Berikutnya &rarr;</Button>
          </div>

          <div className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700">
            <div className="grid grid-cols-7 text-center text-xs font-medium text-muted-foreground dark:text-muted-foreground/60 border-b dark:border-slate-700">
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
                    <span className={`inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded-full ${isToday ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'text-foreground/80 dark:text-muted-foreground/40'}`}>{day}</span>
                    <div className="mt-1 space-y-0.5">
                      {dayEvents.slice(0, 3).map(e => (
                        <div key={e.id} className="text-[10px] px-1 py-0.5 rounded truncate text-white font-medium" style={{ backgroundColor: e.color || TYPE_COLORS[e.type] || '#6b7280' }}>
                          {e.title}
                        </div>
                      ))}
                      {dayEvents.length > 3 && <span className="text-[10px] text-muted-foreground/60">+{dayEvents.length - 3} lainnya</span>}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          <div className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-4">
            <h3 className="font-semibold mb-3">Semua Acara ({events.length})</h3>
            <div className="space-y-2">
              {events.length === 0 ? <p className="text-sm text-muted-foreground/60">Tidak ada acara</p> :
                events.map(e => (
                  <div key={e.id} className="flex items-center gap-3 text-sm border-b dark:border-slate-700 pb-2">
                    <div className="w-3 h-3 rounded-full flex-shrink-0" style={{ backgroundColor: e.color || TYPE_COLORS[e.type] }} />
                    <div className="flex-1">
                      <span className="font-medium text-foreground/90">{e.title}</span>
                      <span className="text-xs text-muted-foreground/60 ml-2">{e.start_date}{e.end_date && e.end_date !== e.start_date ? ` — ${e.end_date}` : ''}</span>
                      {e.location && <span className="text-xs text-muted-foreground/60 ml-2">{e.location}</span>}
                    </div>
                    <Badge variant="default" size="sm">{e.type}</Badge>
                    {user?.role !== 'student' && (
                      <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => handleDelete(e.id)}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                      </Button>
                    )}
                  </div>
                ))
              }
            </div>
          </div>
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-card dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{editEvent ? 'Edit' : 'Tambah'} Acara</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <Input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Judul" required />
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Deskripsi" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <Input type="date" value={form.start_date} onChange={e => setForm(p => ({ ...p, start_date: e.target.value }))} required />
                  <Input type="date" value={form.end_date} onChange={e => setForm(p => ({ ...p, end_date: e.target.value }))} />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <Input type="time" value={form.start_time} onChange={e => setForm(p => ({ ...p, start_time: e.target.value }))} />
                  <Input type="time" value={form.end_time} onChange={e => setForm(p => ({ ...p, end_time: e.target.value }))} />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <Input type="text" value={form.location} onChange={e => setForm(p => ({ ...p, location: e.target.value }))} placeholder="Lokasi" />
                  <Select value={form.type} onChange={e => setForm(p => ({ ...p, type: e.target.value }))} options={[
                    { value: 'academic', label: 'Akademik' }, { value: 'holiday', label: 'Liburan' }, { value: 'exam', label: 'Ujian' },
                    { value: 'meeting', label: 'Rapat' }, { value: 'extracurricular', label: 'Ekstrakurikuler' }, { value: 'other', label: 'Lainnya' },
                  ]} />
                </div>
                <div className="flex justify-end gap-3">
                  <Button variant="outline" type="button" onClick={() => setShowForm(false)}>Batal</Button>
                  <Button type="submit">{editEvent ? 'Update' : 'Buat'}</Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
