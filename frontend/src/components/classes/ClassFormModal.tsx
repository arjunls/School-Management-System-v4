"use client";
import React, { useState } from 'react';
import { classAPI } from '@/lib/api';
import { Input, Select } from '@/components/ui/Input';
import { Button } from '@/components/ui/Button';

interface ClassFormModalProps {
  open: boolean;
  onClose: () => void;
  onSuccess: (message: string) => void;
  classData?: { id: number; name: string; grade_level: number; homeroom_teacher_id?: number | null; capacity: number };
}

const gradeOptions = [7, 8, 9, 10, 11, 12].map((g) => ({ value: String(g), label: `Kelas ${g}` }));

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

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-card text-card-foreground rounded-lg shadow-xl w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-border flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{isEdit ? 'Edit Kelas' : 'Tambah Kelas'}</h2>
          <button onClick={onClose} className="text-muted-foreground hover:text-foreground text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-destructive/10 border-l-4 border-destructive p-3 text-sm text-destructive">{errors._general}</div>}
          <div>
            <Input name="name" label="Nama Kelas" required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} error={errors.name} />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <Select name="grade_level" label="Tingkat" value={String(form.grade_level)} onChange={(e) => setForm({ ...form, grade_level: Number(e.target.value) })} options={gradeOptions} />
            </div>
            <div>
              <Input name="capacity" label="Kapasitas" type="number" min={1} required value={form.capacity} onChange={(e) => setForm({ ...form, capacity: Number(e.target.value) })} error={errors.capacity} />
            </div>
          </div>
          <div>
            <Input name="homeroom_teacher_id" label="ID Guru Kelas" type="number" value={form.homeroom_teacher_id} onChange={(e) => setForm({ ...form, homeroom_teacher_id: e.target.value })} placeholder="Opsional" />
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <Button variant="secondary" type="button" onClick={onClose} disabled={saving}>Batal</Button>
            <Button variant="primary" type="submit" loading={saving}>{saving ? 'Menyimpan...' : isEdit ? 'Perbarui' : 'Simpan'}</Button>
          </div>
        </form>
      </div>
    </div>
  );
}
