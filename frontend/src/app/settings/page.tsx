"use client";
import React, { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';

export default function SettingsPage() {
  const { user } = useAuth();
  const { toast } = useToast();

  const [profile, setProfile] = useState({
    name: user?.name || '',
    phone: user?.phone || '',
    address: user?.address || '',
  });
  const [profileSaving, setProfileSaving] = useState(false);

  const [password, setPassword] = useState({ current_password: '', password: '', password_confirmation: '' });
  const [pwSaving, setPwSaving] = useState(false);
  const [pwErrors, setPwErrors] = useState<Record<string, string>>({});

  const updateProfile = async (e: React.FormEvent) => {
    e.preventDefault();
    setProfileSaving(true);
    try {
      const endpoint = user?.role === 'student' ? 'students' : user?.role === 'teacher' ? 'teachers' : 'users';
      const { default: a } = await import('@/lib/api');
      await a.put(`/${endpoint}/${user?.id}`, profile);
      toast('Profile updated', 'success');
    } catch {
      toast('Failed to update profile', 'error');
    } finally { setProfileSaving(false); }
  };

  const changePassword = async (e: React.FormEvent) => {
    e.preventDefault();
    setPwSaving(true);
    setPwErrors({});
    try {
      const { default: a } = await import('@/lib/api');
      await a.post('/auth/change-password', password);
      toast('Password changed', 'success');
      setPassword({ current_password: '', password: '', password_confirmation: '' });
    } catch (err: unknown) {
      const ae = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (ae?.response?.data?.errors) {
        const flat: Record<string, string> = {};
        for (const [f, msgs] of Object.entries(ae.response.data.errors)) flat[f] = (msgs as string[])[0];
        setPwErrors(flat);
      } else {
        setPwErrors({ _general: ae?.response?.data?.message || 'Failed to change password' });
      }
    } finally { setPwSaving(false); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="max-w-2xl mx-auto space-y-8">
          <PageHeader
            title="Pengaturan"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Pengaturan' }]}
          />

          <div className="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
            <h2 className="text-lg font-semibold mb-4">Edit Profil</h2>
            <form onSubmit={updateProfile} className="space-y-4">
              <Input label="Nama" required value={profile.name} onChange={(e) => setProfile({ ...profile, name: e.target.value })} />
              <div>
                <Input label="Email" value={user?.email || ''} disabled />
                <p className="mt-1 text-xs text-muted-foreground/60">Email tidak dapat diubah.</p>
              </div>
              <Input label="Telepon" value={profile.phone} onChange={(e) => setProfile({ ...profile, phone: e.target.value })} />
              <div>
                <label className="block text-sm font-medium text-foreground/80 mb-1">Alamat</label>
                <textarea rows={2} className="flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-sm transition-all placeholder:text-muted-foreground/60 focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20 border-input" value={profile.address} onChange={(e) => setProfile({ ...profile, address: e.target.value })} />
              </div>
              <Button type="submit" loading={profileSaving}>{profileSaving ? 'Menyimpan...' : 'Simpan Perubahan'}</Button>
            </form>
          </div>

          <div className="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
            <h2 className="text-lg font-semibold mb-4">Ganti Password</h2>
            <form onSubmit={changePassword} className="space-y-4">
              {pwErrors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{pwErrors._general}</div>}
              <Input label="Password Saat Ini *" type="password" required value={password.current_password} onChange={(e) => setPassword({ ...password, current_password: e.target.value })} error={pwErrors.current_password} />
              <div className="grid grid-cols-2 gap-4">
                <Input label="Password Baru *" type="password" required minLength={8} value={password.password} onChange={(e) => setPassword({ ...password, password: e.target.value })} error={pwErrors.password} />
                <Input label="Konfirmasi Password *" type="password" required value={password.password_confirmation} onChange={(e) => setPassword({ ...password, password_confirmation: e.target.value })} error={pwErrors.password_confirmation} />
              </div>
              <Button type="submit" loading={pwSaving}>{pwSaving ? 'Mengganti...' : 'Ganti Password'}</Button>
            </form>
          </div>
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
