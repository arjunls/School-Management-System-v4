"use client";
import React, { useEffect, useState } from 'react';
import { academicYearAPI } from '@/lib/api';
import { useToast } from '@/components/ui/Toast';
import { Input, FormField } from '@/components/ui/Input';
import { Button } from '@/components/ui/Button';

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
      <div className="w-full max-w-lg bg-card text-card-foreground rounded-lg shadow-xl p-6">
        <div className="border-b border-border pb-4 mb-4 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-foreground">{editing ? 'Edit Academic Year' : 'Add Academic Year'}</h2>
          <button onClick={onClose} className="text-muted-foreground hover:text-foreground text-xl leading-none">&times;</button>
        </div>
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            name="name"
            label="Nama Tahun Ajaran"
            type="text"
            value={form.name}
            onChange={e => setForm(p => ({ ...p, name: e.target.value }))}
            required
            error={errors.name?.[0]}
          />
          <div className="grid grid-cols-2 gap-4">
            <Input
              name="start_date"
              label="Tanggal Mulai"
              type="date"
              value={form.start_date}
              onChange={e => setForm(p => ({ ...p, start_date: e.target.value }))}
              required
              error={errors.start_date?.[0]}
            />
            <Input
              name="end_date"
              label="Tanggal Selesai"
              type="date"
              value={form.end_date}
              onChange={e => setForm(p => ({ ...p, end_date: e.target.value }))}
              required
              error={errors.end_date?.[0]}
            />
          </div>
          <FormField label="Status Aktif">
            <label className="flex items-center gap-2 text-sm text-foreground/80 cursor-pointer">
              <input
                type="checkbox"
                checked={form.is_active}
                onChange={e => setForm(p => ({ ...p, is_active: e.target.checked }))}
                className="rounded border-input text-primary focus:ring-ring/50"
              />
              Aktifkan sebagai tahun ajaran aktif
            </label>
          </FormField>
          <div className="flex justify-end gap-3 pt-2">
            <Button variant="secondary" type="button" onClick={onClose}>Batal</Button>
            <Button variant="primary" type="submit" loading={saving}>
              {saving ? 'Menyimpan...' : editing ? 'Perbarui' : 'Simpan'}
            </Button>
          </div>
        </form>
      </div>
    </div>
  );
}
