"use client";
import React, { useState } from 'react';
import { teacherAPI } from '@/lib/api';

interface TeacherFormData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone: string;
  address: string;
  date_of_birth: string;
  gender: string;
  status: string;
}

interface TeacherFormModalProps {
  open: boolean;
  onClose: () => void;
  onSuccess: (message: string) => void;
  teacher?: {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
    date_of_birth?: string;
    gender?: string;
    status: string;
  };
}

export function TeacherFormModal({ open, onClose, onSuccess, teacher }: TeacherFormModalProps) {
  const isEdit = !!teacher;
  const [form, setForm] = useState<TeacherFormData>({
    name: teacher?.name || '',
    email: teacher?.email || '',
    password: '',
    password_confirmation: '',
    phone: teacher?.phone || '',
    address: teacher?.address || '',
    date_of_birth: teacher?.date_of_birth || '',
    gender: teacher?.gender || '',
    status: teacher?.status || 'active',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  if (!open) return null;

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    setErrors((prev) => { const n = { ...prev }; delete n[e.target.name]; return n; });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setErrors({});
    try {
      if (isEdit) {
        const payload: Record<string, unknown> = {
          name: form.name, email: form.email, phone: form.phone || undefined,
          address: form.address || undefined, date_of_birth: form.date_of_birth || undefined,
          gender: form.gender || undefined, status: form.status,
        };
        if (form.password) { payload.password = form.password; payload.password_confirmation = form.password_confirmation; }
        await teacherAPI.update(String(teacher!.id), payload);
        onSuccess('Teacher updated successfully');
      } else {
        await teacherAPI.create({
          name: form.name, email: form.email, password: form.password,
          password_confirmation: form.password_confirmation, phone: form.phone || undefined,
          address: form.address || undefined, date_of_birth: form.date_of_birth || undefined,
          gender: form.gender || undefined, status: form.status,
        });
        onSuccess('Teacher created successfully');
      }
      onClose();
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (axiosErr?.response?.data?.errors) {
        const flat: Record<string, string> = {};
        for (const [field, msgs] of Object.entries(axiosErr.response.data.errors)) flat[field] = (msgs as string[])[0];
        setErrors(flat);
      } else {
        setErrors({ _general: axiosErr?.response?.data?.message || 'Error saving teacher' });
      }
    } finally { setSaving(false); }
  };

  const inputClass = (field: string) =>
    `block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 ${errors[field] ? 'ring-red-500' : 'ring-gray-300'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">{isEdit ? 'Edit Teacher' : 'Add Teacher'}</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div className="sm:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
              <input name="name" required className={inputClass('name')} value={form.name} onChange={handleChange} />
              {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name}</p>}
            </div>
            <div className="sm:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Email *</label>
              <input name="email" type="email" required className={inputClass('email')} value={form.email} onChange={handleChange} />
              {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email}</p>}
            </div>
            {!isEdit && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                  <input name="password" type="password" required className={inputClass('password')} value={form.password} onChange={handleChange} />
                  {errors.password && <p className="mt-1 text-xs text-red-600">{errors.password}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                  <input name="password_confirmation" type="password" required className={inputClass('password_confirmation')} value={form.password_confirmation} onChange={handleChange} />
                </div>
              </>
            )}
            {isEdit && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                  <input name="password" type="password" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.password} onChange={handleChange} placeholder="Leave blank" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                  <input name="password_confirmation" type="password" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.password_confirmation} onChange={handleChange} />
                </div>
              </>
            )}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input name="phone" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.phone} onChange={handleChange} />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Gender</label>
              <select name="gender" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.gender} onChange={handleChange}>
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
              <input name="date_of_birth" type="date" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.date_of_birth} onChange={handleChange} />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select name="status" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.status} onChange={handleChange}>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
              {errors.status && <p className="mt-1 text-xs text-red-600">{errors.status}</p>}
            </div>
            <div className="sm:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
              <textarea name="address" rows={2} className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.address} onChange={handleChange} />
            </div>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" disabled={saving}>Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
              {saving ? 'Saving...' : isEdit ? 'Update Teacher' : 'Create Teacher'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
