"use client";
import React, { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';

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

  const ic = (f: string, errs: Record<string, string>) =>
    `block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm ${errs[f] ? 'ring-red-500' : 'ring-gray-300'}`;

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="max-w-2xl mx-auto space-y-8">
          <h1 className="text-2xl font-bold text-gray-900">Settings</h1>

          {/* Edit Profile */}
          <div className="bg-white rounded-lg shadow border p-6">
            <h2 className="text-lg font-semibold mb-4">Edit Profile</h2>
            <form onSubmit={updateProfile} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input required className={ic('name', {})} value={profile.name} onChange={(e) => setProfile({ ...profile, name: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input className="block w-full rounded-md border-0 px-3 py-2 text-gray-400 bg-gray-50 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm" value={user?.email || ''} disabled />
                <p className="mt-1 text-xs text-gray-400">Email cannot be changed.</p>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input className={ic('phone', {})} value={profile.phone} onChange={(e) => setProfile({ ...profile, phone: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea rows={2} className={ic('address', {})} value={profile.address} onChange={(e) => setProfile({ ...profile, address: e.target.value })} />
              </div>
              <button type="submit" disabled={profileSaving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
                {profileSaving ? 'Saving...' : 'Save Changes'}
              </button>
            </form>
          </div>

          {/* Change Password */}
          <div className="bg-white rounded-lg shadow border p-6">
            <h2 className="text-lg font-semibold mb-4">Change Password</h2>
            <form onSubmit={changePassword} className="space-y-4">
              {pwErrors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{pwErrors._general}</div>}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                <input type="password" required className={ic('current_password', pwErrors)} value={password.current_password} onChange={(e) => setPassword({ ...password, current_password: e.target.value })} />
                {pwErrors.current_password && <p className="mt-1 text-xs text-red-600">{pwErrors.current_password}</p>}
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                  <input type="password" required minLength={8} className={ic('password', pwErrors)} value={password.password} onChange={(e) => setPassword({ ...password, password: e.target.value })} />
                  {pwErrors.password && <p className="mt-1 text-xs text-red-600">{pwErrors.password}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                  <input type="password" required className={ic('password_confirmation', pwErrors)} value={password.password_confirmation} onChange={(e) => setPassword({ ...password, password_confirmation: e.target.value })} />
                  {pwErrors.password_confirmation && <p className="mt-1 text-xs text-red-600">{pwErrors.password_confirmation}</p>}
                </div>
              </div>
              <button type="submit" disabled={pwSaving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
                {pwSaving ? 'Changing...' : 'Change Password'}
              </button>
            </form>
          </div>
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
