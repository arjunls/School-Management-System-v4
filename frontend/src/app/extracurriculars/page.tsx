"use client";
import React, { useEffect, useState } from 'react';
import { extracurricularAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function ExtracurricularsPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [items, setItems] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editItem, setEditItem] = useState<any>(null);
  const [form, setForm] = useState({ name: '', description: '', coach: '', day: '', start_time: '', end_time: '', location: '', max_participants: '' });

  const fetch = async () => {
    try { setLoading(true); const res = await extracurricularAPI.getAll(); setItems(res.data?.data ?? []); } catch {} finally { setLoading(false); }
  };
  useEffect(() => { fetch(); }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { ...form };
      if (data.max_participants) data.max_participants = Number(data.max_participants);
      if (editItem) { await extracurricularAPI.update(editItem.id, data); toast('Updated', 'success'); }
      else { await extracurricularAPI.create(data); toast('Created', 'success'); }
      setShowForm(false); setEditItem(null); setForm({ name: '', description: '', coach: '', day: '', start_time: '', end_time: '', location: '', max_participants: '' }); fetch();
    } catch { toast('Failed', 'error'); }
  };

  const handleJoin = async (id: number) => { try { await extracurricularAPI.join(id); toast('Joined!', 'success'); fetch(); } catch { toast('Failed', 'error'); } };
  const handleLeave = async (id: number) => { try { await extracurricularAPI.leave(id); toast('Left', 'success'); fetch(); } catch { toast('Failed', 'error'); } };
  const handleDelete = async (id: number) => { try { await extracurricularAPI.delete(id); toast('Deleted', 'success'); fetch(); } catch { toast('Failed', 'error'); } };

  const openEdit = (item: any) => { setEditItem(item); setForm({ name: item.name, description: item.description ?? '', coach: item.coach ?? '', day: item.day ?? '', start_time: item.start_time ?? '', end_time: item.end_time ?? '', location: item.location ?? '', max_participants: String(item.max_participants ?? '') }); setShowForm(true); };

  const days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Extracurricular Activities</h1>
            {user?.role !== 'student' && <button onClick={() => { setEditItem(null); setForm({ name: '', description: '', coach: '', day: '', start_time: '', end_time: '', location: '', max_participants: '' }); setShowForm(true); }} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Add Activity</button>}
          </div>

          {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
            items.length === 0 ? <div className="text-center py-12 text-gray-500">No activities yet</div> :
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              {items.map(item => {
                const joined = user?.role === 'student' && item.active_participants_count && item.active_participants?.some((p: any) => p.id === user.id);
                return (
                  <div key={item.id} className="bg-white rounded-lg shadow border p-4 flex flex-col">
                    <div className="flex-1">
                      <h3 className="font-semibold text-gray-900">{item.name}</h3>
                      {item.description && <p className="text-xs text-gray-500 mt-1">{item.description}</p>}
                      <div className="mt-3 space-y-1 text-xs text-gray-600">
                        {item.coach && <p>Coach: {item.coach}</p>}
                        {item.day && <p>Schedule: {item.day}{item.start_time ? ` ${item.start_time}-${item.end_time ?? ''}` : ''}</p>}
                        {item.location && <p>Location: {item.location}</p>}
                        <p>Participants: {item.active_participants_count ?? 0}{item.max_participants ? ` / ${item.max_participants}` : ''}</p>
                      </div>
                    </div>
                    <div className="mt-4 flex gap-2 justify-end">
                      {user?.role === 'student' ? (
                        joined ? <button onClick={() => handleLeave(item.id)} className="px-3 py-1.5 text-xs border border-red-300 text-red-600 rounded-md">Leave</button>
                        : <button onClick={() => handleJoin(item.id)} className="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-md">Join</button>
                      ) : (
                        <>
                          <button onClick={() => openEdit(item)} className="px-3 py-1.5 text-xs border border-gray-300 rounded-md">Edit</button>
                          {user?.role === 'admin' && <button onClick={() => handleDelete(item.id)} className="px-3 py-1.5 text-xs border border-red-300 text-red-600 rounded-md">Delete</button>}
                        </>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          }
        </div>

        {/* Form Modal */}
        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">{editItem ? 'Edit' : 'Add'} Activity</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))} placeholder="Name" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="text" value={form.coach} onChange={e => setForm(p => ({ ...p, coach: e.target.value }))} placeholder="Coach" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <textarea value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="grid grid-cols-3 gap-3">
                  <select value={form.day} onChange={e => setForm(p => ({ ...p, day: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Select Day</option>
                    {days.map(d => <option key={d} value={d}>{d}</option>)}
                  </select>
                  <input type="time" value={form.start_time} onChange={e => setForm(p => ({ ...p, start_time: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="time" value={form.end_time} onChange={e => setForm(p => ({ ...p, end_time: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={form.location} onChange={e => setForm(p => ({ ...p, location: e.target.value }))} placeholder="Location" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="number" value={form.max_participants} onChange={e => setForm(p => ({ ...p, max_participants: e.target.value }))} placeholder="Max participants" min={1} className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">{editItem ? 'Update' : 'Create'}</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
