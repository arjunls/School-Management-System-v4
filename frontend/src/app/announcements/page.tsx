"use client";
import React, { useEffect, useState } from 'react';
import { announcementAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input, Select } from '@/components/ui/Input';

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

  const roleVariant = (role: string) => {
    if (role === 'admin') return 'danger' as const;
    if (role === 'teacher') return 'info' as const;
    if (role === 'student') return 'success' as const;
    if (role === 'parent') return 'warning' as const;
    return 'default' as const;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Pengumuman"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Pengumuman' }]}
            action={
              user?.role !== 'student' && user?.role !== 'parent' && (
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setEditItem(null); setForm({ title: '', content: '', target_role: 'all', publish_at: '', expires_at: '' }); setShowForm(true); }}
                >
                  Buat
                </Button>
              )
            }
          />

          {loading ? <div className="text-center py-12 text-muted-foreground">Memuat...</div> :
            items.length === 0 ? <div className="text-center py-12 text-muted-foreground">Belum ada pengumuman</div> :
            <div className="space-y-4">
              {items.map(a => (
                <div key={a.id} className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-5">
                  <div className="flex items-center justify-between mb-2">
                    <h3 className="font-semibold text-foreground dark:text-white">{a.title}</h3>
                    <div className="flex items-center gap-2">
                      <Badge variant={roleVariant(a.target_role)}>{a.target_role}</Badge>
                      {user?.role !== 'student' && user?.role !== 'parent' && (
                        <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => handleDelete(a.id)}>
                          <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </Button>
                      )}
                    </div>
                  </div>
                  <p className="text-sm text-foreground/80 dark:text-muted-foreground/40 whitespace-pre-wrap">{a.content}</p>
                  <div className="mt-3 flex items-center justify-between text-xs text-muted-foreground/60 dark:text-muted-foreground">
                    <span>Oleh {a.author?.name}</span>
                    <span>{new Date(a.created_at).toLocaleDateString()}</span>
                  </div>
                </div>
              ))}
            </div>
          }
        </div>

        {showForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-card dark:bg-slate-800 rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4 dark:text-white">{editItem ? 'Edit' : 'Buat'} Pengumuman</h2>
              <form onSubmit={handleSave} className="space-y-4">
                <Input type="text" value={form.title} onChange={e => setForm(p => ({ ...p, title: e.target.value }))} placeholder="Judul" required />
                <textarea value={form.content} onChange={e => setForm(p => ({ ...p, content: e.target.value }))} placeholder="Konten" rows={4} required className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                <Select value={form.target_role} onChange={e => setForm(p => ({ ...p, target_role: e.target.value }))} options={[
                  { value: 'all', label: 'Semua' }, { value: 'admin', label: 'Admin' }, { value: 'teacher', label: 'Guru' }, { value: 'student', label: 'Siswa' }, { value: 'parent', label: 'Orang Tua' },
                ]} />
                <div className="grid grid-cols-2 gap-3">
                  <Input type="datetime-local" value={form.publish_at} onChange={e => setForm(p => ({ ...p, publish_at: e.target.value }))} placeholder="Terbit pada" />
                  <Input type="datetime-local" value={form.expires_at} onChange={e => setForm(p => ({ ...p, expires_at: e.target.value }))} placeholder="Kadaluarsa pada" />
                </div>
                <div className="flex justify-end gap-3">
                  <Button variant="outline" type="button" onClick={() => setShowForm(false)}>Batal</Button>
                  <Button type="submit">{editItem ? 'Update' : 'Buat'}</Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
