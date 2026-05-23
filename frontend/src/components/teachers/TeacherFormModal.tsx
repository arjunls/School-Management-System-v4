"use client";
import React, { useState } from 'react';
import { teacherAPI } from '@/lib/api';
import { Input, Select, FormField } from '@/components/ui/Input';
import { Button } from '@/components/ui/Button';

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

const genderOptions = [
  { value: 'male', label: 'Laki-laki' },
  { value: 'female', label: 'Perempuan' },
  { value: 'other', label: 'Lainnya' },
];

const statusOptions = [
  { value: 'active', label: 'Aktif' },
  { value: 'inactive', label: 'Tidak Aktif' },
  { value: 'suspended', label: 'Ditangguhkan' },
];

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

  return (
    <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/40" onClick={onClose}>
      <div className="bg-card text-card-foreground rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
        <div className="px-6 py-4 border-b border-border flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{isEdit ? 'Edit Guru' : 'Tambah Guru'}</h2>
          <button onClick={onClose} className="text-muted-foreground hover:text-foreground text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          {errors._general && <div className="bg-destructive/10 border-l-4 border-destructive p-3 text-sm text-destructive">{errors._general}</div>}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div className="sm:col-span-2">
              <Input name="name" label="Nama Lengkap" required value={form.name} onChange={handleChange} error={errors.name} />
            </div>
            <div className="sm:col-span-2">
              <Input name="email" label="Email" type="email" required value={form.email} onChange={handleChange} error={errors.email} />
            </div>
            {!isEdit && (
              <>
                <div>
                  <Input name="password" label="Kata Sandi" type="password" required value={form.password} onChange={handleChange} error={errors.password} />
                </div>
                <div>
                  <Input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" required value={form.password_confirmation} onChange={handleChange} />
                </div>
              </>
            )}
            {isEdit && (
              <>
                <div>
                  <Input name="password" label="Kata Sandi Baru" type="password" value={form.password} onChange={handleChange} placeholder="Kosongkan jika tidak ingin mengubah" />
                </div>
                <div>
                  <Input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" value={form.password_confirmation} onChange={handleChange} />
                </div>
              </>
            )}
            <div>
              <Input name="phone" label="Nomor Telepon" value={form.phone} onChange={handleChange} />
            </div>
            <div>
              <Select name="gender" label="Jenis Kelamin" value={form.gender} onChange={handleChange} options={genderOptions} placeholder="Pilih" />
            </div>
            <div>
              <Input name="date_of_birth" label="Tanggal Lahir" type="date" value={form.date_of_birth} onChange={handleChange} />
            </div>
            <div>
              <Select name="status" label="Status" value={form.status} onChange={handleChange} options={statusOptions} error={errors.status} />
            </div>
            <div className="sm:col-span-2">
              <FormField label="Alamat">
                <textarea
                  name="address"
                  rows={2}
                  className="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20"
                  value={form.address}
                  onChange={handleChange}
                />
              </FormField>
            </div>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <Button variant="secondary" type="button" onClick={onClose} disabled={saving}>Batal</Button>
            <Button variant="primary" type="submit" loading={saving}>
              {saving ? 'Menyimpan...' : isEdit ? 'Perbarui' : 'Simpan'}
            </Button>
          </div>
        </form>
      </div>
    </div>
  );
}
