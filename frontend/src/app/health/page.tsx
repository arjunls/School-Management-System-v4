"use client";
import React, { useEffect, useState } from 'react';
import { healthAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function HealthPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [studentId, setStudentId] = useState('');
  const [record, setRecord] = useState<any>(null);
  const [students, setStudents] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [editing, setEditing] = useState(false);
  const [form, setForm] = useState({ blood_type: '', allergies: '', medical_conditions: '', medications: '', emergency_contact_name: '', emergency_contact_phone: '', notes: '' });

  useEffect(() => {
    if (user?.role === 'student') { setStudentId(String(user.id)); fetchRecord(String(user.id)); }
    else { studentAPI.getList({ per_page: 50 }).then(r => setStudents(r.data?.data ?? [])).catch(() => {}); }
  }, [user]);

  const fetchRecord = async (id: string) => {
    if (!id) return;
    setLoading(true); setRecord(null);
    try {
      const res = await healthAPI.get(Number(id));
      const data = res.data?.data;
      setRecord(data);
      if (data) {
        setForm({ blood_type: data.blood_type ?? '', allergies: data.allergies ?? '', medical_conditions: data.medical_conditions ?? '', medications: data.medications ?? '', emergency_contact_name: data.emergency_contact_name ?? '', emergency_contact_phone: data.emergency_contact_phone ?? '', notes: data.notes ?? '' });
      } else {
        setForm({ blood_type: '', allergies: '', medical_conditions: '', medications: '', emergency_contact_name: '', emergency_contact_phone: '', notes: '' });
      }
    } catch { toast('Failed to load', 'error'); } finally { setLoading(false); }
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try { await healthAPI.save(Number(studentId), form); toast('Saved', 'success'); setEditing(false); fetchRecord(studentId); }
    catch { toast('Failed', 'error'); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student', 'parent']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-foreground dark:text-white">Health Records</h1>
          </div>

          {user?.role !== 'student' && (
            <div className="flex gap-3">
              <select value={studentId} onChange={e => { setStudentId(e.target.value); fetchRecord(e.target.value); }} className="rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <option value="">Select Student</option>
                {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
              </select>
            </div>
          )}

          {loading ? <div className="text-center py-12 text-muted-foreground dark:text-muted-foreground/60">Loading...</div> :
            studentId ? (
              <div className="max-w-2xl">
                {!editing ? (
                  <div className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-6 space-y-4">
                    {record ? (
                      <>
                        <div className="grid grid-cols-2 gap-4 text-sm">
                          <div><span className="text-muted-foreground/60">Blood Type</span><p className="font-medium dark:text-white">{record.blood_type || '—'}</p></div>
                          <div><span className="text-muted-foreground/60">Emergency Contact</span><p className="font-medium dark:text-white">{record.emergency_contact_name || '—'} {record.emergency_contact_phone ? `(${record.emergency_contact_phone})` : ''}</p></div>
                        </div>
                        {record.allergies && <div><span className="text-sm text-muted-foreground/60">Allergies</span><p className="text-sm dark:text-white">{record.allergies}</p></div>}
                        {record.medical_conditions && <div><span className="text-sm text-muted-foreground/60">Medical Conditions</span><p className="text-sm dark:text-white">{record.medical_conditions}</p></div>}
                        {record.medications && <div><span className="text-sm text-muted-foreground/60">Medications</span><p className="text-sm dark:text-white">{record.medications}</p></div>}
                        {record.notes && <div><span className="text-sm text-muted-foreground/60">Notes</span><p className="text-sm dark:text-white">{record.notes}</p></div>}
                      </>
                    ) : (
                      <p className="text-muted-foreground/60 text-sm">No health record yet.</p>
                    )}
                    {user?.role !== 'student' && <button onClick={() => setEditing(true)} className="px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-md">Edit</button>}
                  </div>
                ) : (
                  <form onSubmit={handleSave} className="bg-card dark:bg-slate-800 rounded-lg shadow border dark:border-slate-700 p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-3">
                      <select value={form.blood_type} onChange={e => setForm(p => ({ ...p, blood_type: e.target.value }))} className="rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                        <option value="">Blood Type</option>
                        <option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option>
                        <option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option>
                      </select>
                      <input type="text" value={form.emergency_contact_name} onChange={e => setForm(p => ({ ...p, emergency_contact_name: e.target.value }))} placeholder="Emergency Contact Name" className="rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    </div>
                    <input type="text" value={form.emergency_contact_phone} onChange={e => setForm(p => ({ ...p, emergency_contact_phone: e.target.value }))} placeholder="Emergency Phone" className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    <textarea value={form.allergies} onChange={e => setForm(p => ({ ...p, allergies: e.target.value }))} placeholder="Allergies" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    <textarea value={form.medical_conditions} onChange={e => setForm(p => ({ ...p, medical_conditions: e.target.value }))} placeholder="Medical Conditions" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    <textarea value={form.medications} onChange={e => setForm(p => ({ ...p, medications: e.target.value }))} placeholder="Medications" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    <textarea value={form.notes} onChange={e => setForm(p => ({ ...p, notes: e.target.value }))} placeholder="Notes" rows={2} className="block w-full rounded-md border border-border dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" />
                    <div className="flex justify-end gap-3">
                      <button type="button" onClick={() => setEditing(false)} className="px-4 py-2 text-sm border border-border dark:border-slate-600 dark:text-muted-foreground/20 rounded-md">Cancel</button>
                      <button type="submit" className="px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-md">Save</button>
                    </div>
                  </form>
                )}
              </div>
            ) : (
              <div className="text-center py-12 text-muted-foreground dark:text-muted-foreground/60">Select a student to view health record</div>
            )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
