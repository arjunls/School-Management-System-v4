"use client";
import React, { useEffect, useState } from 'react';
import { feeAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { DataTable } from '@/components/ui/DataTable';

export default function FeesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [tab, setTab] = useState<'types' | 'invoices'>('types');
  const [types, setTypes] = useState<any[]>([]);
  const [invoices, setInvoices] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const [showTypeForm, setShowTypeForm] = useState(false);
  const [showInvoiceForm, setShowInvoiceForm] = useState(false);
  const [showPayForm, setShowPayForm] = useState(false);
  const [selectedInvoice, setSelectedInvoice] = useState<any>(null);
  const [students, setStudents] = useState<any[]>([]);
  const [typeForm, setTypeForm] = useState({ name: '', amount: '', frequency: 'once', description: '' });
  const [invoiceForm, setInvoiceForm] = useState({ fee_type_id: '', student_id: '', amount: '', due_date: '', notes: '' });
  const [payForm, setPayForm] = useState({ amount: '', payment_date: new Date().toISOString().slice(0,10), payment_method: 'cash', reference_no: '', notes: '' });

  const fetchTypes = async () => { try { const res = await feeAPI.getTypes(); setTypes(res.data?.data ?? []); } catch {} };
  const fetchInvoices = async () => { try { setLoading(true); const res = await feeAPI.getInvoices(); setInvoices(res.data?.data?.data ?? []); } finally { setLoading(false); } };

  useEffect(() => { tab === 'types' ? fetchTypes() : fetchInvoices(); }, [tab]);

  const handleTypeSave = async (e: React.FormEvent) => {
    e.preventDefault(); try { await feeAPI.createType({ ...typeForm, amount: Number(typeForm.amount) }); toast('Fee type created', 'success'); setShowTypeForm(false); setTypeForm({ name: '', amount: '', frequency: 'once', description: '' }); fetchTypes(); } catch { toast('Failed', 'error'); }
  };

  const handleInvoiceCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const data: any = { fee_type_id: Number(invoiceForm.fee_type_id), student_id: Number(invoiceForm.student_id), due_date: invoiceForm.due_date };
      if (invoiceForm.amount) data.amount = Number(invoiceForm.amount);
      if (invoiceForm.notes) data.notes = invoiceForm.notes;
      await feeAPI.createInvoice(data); toast('Invoice created', 'success'); setShowInvoiceForm(false); setInvoiceForm({ fee_type_id: '', student_id: '', amount: '', due_date: '', notes: '' }); fetchInvoices();
    } catch { toast('Failed', 'error'); }
  };

  const handlePay = async (e: React.FormEvent) => {
    e.preventDefault();
    try { await feeAPI.payInvoice(selectedInvoice.id, { ...payForm, amount: Number(payForm.amount) }); toast('Payment recorded', 'success'); setShowPayForm(false); setSelectedInvoice(null); setPayForm({ amount: '', payment_date: new Date().toISOString().slice(0,10), payment_method: 'cash', reference_no: '', notes: '' }); fetchInvoices(); } catch { toast('Failed', 'error'); }
  };

  const openPayForm = (inv: any) => { setSelectedInvoice(inv); setPayForm(p => ({ ...p, amount: String(inv.amount - (inv.paid_amount ?? 0)) })); setShowPayForm(true); };

  const statusVariant = (s: string) => {
    if (s === 'paid') return 'success' as const;
    if (s === 'overdue') return 'danger' as const;
    if (s === 'partial') return 'warning' as const;
    return 'default' as const;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Manajemen Biaya"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Biaya' }]}
            action={
              <div className="flex flex-wrap gap-2">
                <button onClick={() => setTab('types')} className={`px-3 py-2 text-sm rounded-md ${tab === 'types' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Jenis Biaya</button>
                <button onClick={() => setTab('invoices')} className={`px-3 py-2 text-sm rounded-md ${tab === 'invoices' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Invoice</button>
              </div>
            }
          />

          {tab === 'types' ? (
            <>
              {user?.role === 'admin' && <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>} onClick={() => setShowTypeForm(true)}>Tambah Jenis Biaya</Button>}
              <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {types.map(t => (
                  <div key={t.id} className="rounded-xl border bg-card text-card-foreground shadow-sm border p-4">
                    <h3 className="font-semibold text-foreground">{t.name}</h3>
                    {t.description && <p className="text-xs text-muted-foreground mt-1">{t.description}</p>}
                    <p className="text-lg font-bold text-blue-600 mt-2">Rp {Number(t.amount).toLocaleString()}</p>
                    <Badge variant="info" size="sm">{t.frequency}</Badge>
                  </div>
                ))}
                {types.length === 0 && !loading && <p className="col-span-full text-center text-muted-foreground py-8">Belum ada jenis biaya</p>}
              </div>
            </>
          ) : (
            <>
              {user?.role === 'admin' && (
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setShowInvoiceForm(true); studentAPI.getList({ per_page: 50 }).then(r => setStudents(r.data?.data ?? [])).catch(() => {}); }}
                >
                  Invoice Baru
                </Button>
              )}
              <DataTable
                columns={[
                  { key: 'student', label: 'Siswa', render: (row) => row.student?.name },
                  { key: 'fee_type', label: 'Jenis Biaya', render: (row) => row.fee_type?.name },
                  { key: 'amount', label: 'Jumlah', className: 'text-right', render: (row) => `Rp ${Number(row.amount).toLocaleString()}` },
                  { key: 'due_date', label: 'Jatuh Tempo', render: (row) => new Date(row.due_date).toLocaleDateString() },
                  { key: 'status', label: 'Status', render: (row) => <Badge variant={statusVariant(row.status)}>{row.status}</Badge> },
                  { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                    row.status !== 'paid' && (user?.role === 'admin' || user?.role === 'student') ? (
                      <Button variant="ghost" size="sm" onClick={() => openPayForm(row)}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                        Bayar
                      </Button>
                    ) : null
                  )},
                ]}
                data={invoices}
                keyExtractor={(row) => row.id}
                loading={loading}
                emptyMessage="Belum ada invoice."
              />
            </>
          )}
        </div>

        {showTypeForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Tambah Jenis Biaya</h2>
              <form onSubmit={handleTypeSave} className="space-y-4">
                <input type="text" value={typeForm.name} onChange={e => setTypeForm(p => ({ ...p, name: e.target.value }))} placeholder="Nama" required className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input type="number" value={typeForm.amount} onChange={e => setTypeForm(p => ({ ...p, amount: e.target.value }))} placeholder="Jumlah" required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <select value={typeForm.frequency} onChange={e => setTypeForm(p => ({ ...p, frequency: e.target.value }))} className="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="once">Sekali</option><option value="monthly">Bulanan</option><option value="quarterly">Triwulan</option><option value="yearly">Tahunan</option>
                  </select>
                </div>
                <textarea value={typeForm.description} onChange={e => setTypeForm(p => ({ ...p, description: e.target.value }))} placeholder="Deskripsi" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowTypeForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Batal</button>
                  <Button type="submit">Buat</Button>
                </div>
              </form>
            </div>
          </div>
        )}

        {showInvoiceForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Invoice Baru</h2>
              <form onSubmit={handleInvoiceCreate} className="space-y-4">
                <select value={invoiceForm.student_id} onChange={e => setInvoiceForm(p => ({ ...p, student_id: e.target.value }))} required className="block w-full rounded-md border border-border px-3 py-2 text-sm">
                  <option value="">Pilih Siswa</option>
                  {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
                </select>
                <select value={invoiceForm.fee_type_id} onChange={e => { setInvoiceForm(p => ({ ...p, fee_type_id: e.target.value })); const t = types.find(t => t.id === Number(e.target.value)); if (t) setInvoiceForm(p => ({ ...p, amount: String(t.amount) })); }} required className="block w-full rounded-md border border-border px-3 py-2 text-sm">
                  <option value="">Pilih Jenis Biaya</option>
                  {types.map(t => <option key={t.id} value={t.id}>{t.name} — Rp {Number(t.amount).toLocaleString()}</option>)}
                </select>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input type="number" value={invoiceForm.amount} onChange={e => setInvoiceForm(p => ({ ...p, amount: e.target.value }))} placeholder="Jumlah (otomatis)" className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="date" value={invoiceForm.due_date} onChange={e => setInvoiceForm(p => ({ ...p, due_date: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <textarea value={invoiceForm.notes} onChange={e => setInvoiceForm(p => ({ ...p, notes: e.target.value }))} placeholder="Catatan" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowInvoiceForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Batal</button>
                  <Button type="submit">Buat</Button>
                </div>
              </form>
            </div>
          </div>
        )}

        {showPayForm && selectedInvoice && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Bayar Invoice</h2>
              <p className="text-sm text-foreground/70 mb-4">{selectedInvoice.fee_type?.name} — Rp {Number(selectedInvoice.amount).toLocaleString()}</p>
              <form onSubmit={handlePay} className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input type="number" value={payForm.amount} onChange={e => setPayForm(p => ({ ...p, amount: e.target.value }))} placeholder="Jumlah" required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="date" value={payForm.payment_date} onChange={e => setPayForm(p => ({ ...p, payment_date: e.target.value }))} required className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <select value={payForm.payment_method} onChange={e => setPayForm(p => ({ ...p, payment_method: e.target.value }))} className="rounded-md border border-border px-3 py-2 text-sm">
                    <option value="cash">Tunai</option><option value="transfer">Transfer</option><option value="cheque">Cek</option><option value="other">Lainnya</option>
                  </select>
                  <input type="text" value={payForm.reference_no} onChange={e => setPayForm(p => ({ ...p, reference_no: e.target.value }))} placeholder="No. Referensi" className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <textarea value={payForm.notes} onChange={e => setPayForm(p => ({ ...p, notes: e.target.value }))} placeholder="Catatan" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowPayForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Batal</button>
                  <Button type="submit">Bayar</Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
