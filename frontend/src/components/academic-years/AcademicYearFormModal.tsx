"use client";
import React, { useEffect, useState } from 'react';
import { academicYearAPI } from '@/lib/api';
import { useToast } from '@/components/ui/Toast';

interface Props {
  open: boolean;
  onClose: () => void;
  onSuccess: () => void;
  editing?: { id: number; name: string; start_date: string; end_date: string; is_active: boolean } | null;
}

export function AcademicYearFormModal({ open, onClose, onSuccess, editing }: Props) {
  const { toast } = useToast();
  const [form, setForm] = useState({ name: '', start_date: '', end_date: '', is_active: false });
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  useEffect(() => {
    if (editing) {
      setForm({
        name: editing.name,
        start_date: editing.start_date.slice(0, 10),
        end_date: editing.end_date.slice(0, 10),
        is_active: editing.is_active,
      });
    } else {
      setForm({ name: '', start_date: '', end_date: '', is_active: false });
    }
    setErrors({});
  }, [editing, open]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setErrors({});
    try {
      if (editing) {
        await academicYearAPI.update(editing.id, form);
        toast('Academic year updated', 'success');
      } else {
        await academicYearAPI.create(form);
        toast('Academic year created', 'success');
      }
      onSuccess();
      onClose();
    } catch (err: any) {
      if (err?.response?.data?.errors) setErrors(err.response.data.errors);
      else toast(err?.response?.data?.message || 'Failed to save', 'error');
    } finally {
      setSaving(false);
    }
  };

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">{editing ? 'Edit Academic Year' : 'Add Academic Year'}</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" value={form.name} onChange={e => setForm(p => ({ ...p, name: e.target.value }))}
              className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none" required />
            {errors.name?.map((e, i) => <p key={i} className="text-red-500 text-xs mt-1">{e}</p>)}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700">Start Date</label>
              <input type="date" value={form.start_date} onChange={e => setForm(p => ({ ...p, start_date: e.target.value }))}
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none" required />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700">End Date</label>
              <input type="date" value={form.end_date} onChange={e => setForm(p => ({ ...p, end_date: e.target.value }))}
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none" required />
            </div>
          </div>
          <label className="flex items-center gap-2 text-sm">
            <input type="checkbox" checked={form.is_active} onChange={e => setForm(p => ({ ...p, is_active: e.target.checked }))}
              className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            Set as active academic year
          </label>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving}
              className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">{saving ? 'Saving...' : 'Save'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
