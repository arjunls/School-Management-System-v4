"use client";
import React, { useState } from 'react';
import { classAPI } from '@/lib/api';

interface ClassFormModalProps {
  open: boolean;
  onClose: () => void;
  onSuccess: (message: string) => void;
  classData?: { id: number; name: string; grade_level: number; homeroom_teacher_id?: number | null; capacity: number };
}

export function ClassFormModal({ open, onClose, onSuccess, classData }: ClassFormModalProps) {
  const isEdit = !!classData;
  const [form, setForm] = useState({
    name: classData?.name || '', grade_level: classData?.grade_level || 10,
    homeroom_teacher_id: classData?.homeroom_teacher_id ?? '', capacity: classData?.capacity ?? 30,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  if (!open) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true); setErrors({});
    try {
      const payload = { ...form, homeroom_teacher_id: form.homeroom_teacher_id === '' ? null : Number(form.homeroom_teacher_id) };
      if (isEdit) { await classAPI.update(String(classData!.id), payload); onSuccess('Class updated'); }
      else { await classAPI.create(payload); onSuccess('Class created'); }
      onClose();
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (axiosErr?.response?.data?.errors) {
        const flat: Record<string, string> = {};
        for (const [field, msgs] of Object.entries(axiosErr.response.data.errors)) flat[field] = (msgs as string[])[0];
        setErrors(flat);
      } else setErrors({ _general: axiosErr?.response?.data?.message || 'Error saving class' });
    } finally { setSaving(false); }
  };

  const ic = (f: string) => `block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 ${errors[f] ? 'ring-red-500' : 'ring-gray-300'}`;

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">{isEdit ? 'Edit Class' : 'Add Class'}</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">{errors._general}</div>}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
            <input name="name" required className={ic('name')} value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
            {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Grade Level *</label>
              <select name="grade_level" className={ic('grade_level')} value={form.grade_level} onChange={(e) => setForm({ ...form, grade_level: Number(e.target.value) })}>
                {[7, 8, 9, 10, 11, 12].map((g) => <option key={g} value={g}>Grade {g}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
              <input name="capacity" type="number" min={1} required className={ic('capacity')} value={form.capacity} onChange={(e) => setForm({ ...form, capacity: Number(e.target.value) })} />
              {errors.capacity && <p className="mt-1 text-xs text-red-600">{errors.capacity}</p>}
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Homeroom Teacher ID</label>
            <input name="homeroom_teacher_id" type="number" className="block w-full rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={form.homeroom_teacher_id} onChange={(e) => setForm({ ...form, homeroom_teacher_id: e.target.value })} placeholder="Optional" />
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} disabled={saving} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">{saving ? 'Saving...' : isEdit ? 'Update Class' : 'Create Class'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
