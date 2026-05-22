"use client";
import React, { useEffect, useState } from 'react';
import { announcementAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function AnnouncementsPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [items, setItems] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editItem, setEditItem] = useState<any>(null);
  const [form, setForm] = useState({ title: '', content: '', target_role: 'all', publish_at: '', expires_at: '' });

  const fetch = async () => {
    try { setLoading(true); const res = await announcementAPI.getAll(); setItems(res.data?.data?.data ?? res.data?.data ?? []); } finally { setLoading(false); }
  };
  useEffect(() => { fetch(); }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { ...form };
      if (!data.publish_at) delete data.publish_at;
      if (!data.expires_at) delete data.expires_at;
      if (editItem) { await announcementAPI.update(editItem.id, data); toast('Updated', 'success'); }
      else { await announcementAPI.create(data); toast('Created', 'success'); }
      setShowForm(false); setEditItem(null); setForm({ title: '', content: '', target_role: 'all', publish_at: '', expires_at: '' }); fetch();
    } catch { toast('Failed', 'error'); }
  };

  const handleDelete = async (id: number) => { try { await announcementAPI.delete(id); toast('Deleted', 'success'); fetch(); } catch { toast('Failed', 'error'); } };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Announcements</h1>
            {user?.role !== 'student' && user?.role !== 'parent' && <button onClick={() => { setEditItem(null); setForm({ title: '', content: '', target_role: 'all', publish_at: '', expires_at: '' }); setShowForm(true); }} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ New Announcement</button>}
          </div>

          {loading ? <div className="text-center py-12 text-gray-500 dark:text-gray-400">Loading...</div> :
            items.length === 0 ? <div className="text-center py-12 text-gray-500 dark:text-gray-400">No announcements</div> :
            <div className="space-y-4">
              {items.map(a => (
                <div key={a.id} className="bg-white dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-5">
                  <div className="flex items-center justify-between mb-2">
                    <h3 className="font-semibold text-gray-900 dark:text-white">{a.title}</h3>
                    <div className="flex items-center gap-2">
                      <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{a.target_role}</span>
                      {user?.role !== 'student' && user?.role !== 'parent' && (
                        <button onClick={() => handleDelete(a.id)} className="text-red-500 text-xs hover:underline">Delete</button>
                      )}
                    </div>
                  </div>
                  <p className="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{a.content}</p>
                  <div className="mt-3 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                    <span>By {a.author?.name}</span>
                    <span>{new Date(a.created_at).toLocaleDateString()}</span>
                  </div>
                </div>
              ))}
            </div>
          }
        </div>

        {/* Form Modal */}
        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{editItem ? 'Edit' : 'New'} Announcement</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Title" required className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <textarea value={form.content} onChange={e => setForm(p => ({ ...p, content: e.target.value }))} placeholder="Content" rows={4} required className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <select value={form.target_role} onChange={e => setForm(p => ({ ...p, target_role: e.target.value }))} className="block w-full rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                  <option value="all">All</option><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="student">Student</option><option value="parent">Parent</option>
                </select>
                <div className="grid grid-cols-2 gap-3">
                  <input type="datetime-local" value={form.publish_at} onChange={e => setForm(p => ({ ...p, publish_at: e.target.value }))} placeholder="Publish at" className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                  <input type="datetime-local" value={form.expires_at} onChange={e => setForm(p => ({ ...p, expires_at: e.target.value }))} placeholder="Expires at" className="rounded-md border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                </div>
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:text-gray-200 rounded-md">Cancel</button>
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
