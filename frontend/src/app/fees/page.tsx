"use client";
import React, { useEffect, useState } from 'react';
import { feeAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function FeesPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [tab, setTab] = useState<'types' | 'invoices'>('types');
  const [types, setTypes] = useState<any[]>([]);
  const [invoices, setInvoices] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Modals
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

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Fee Management</h1>
            <div className="flex gap-2">
              <button onClick={() => setTab('types')} className={`px-3 py-2 text-sm rounded-md ${tab === 'types' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300'}`}>Fee Types</button>
              <button onClick={() => setTab('invoices')} className={`px-3 py-2 text-sm rounded-md ${tab === 'invoices' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300'}`}>Invoices</button>
            </div>
          </div>

          {tab === 'types' ? (
            <>
              {user?.role === 'admin' && <button onClick={() => setShowTypeForm(true)} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Add Fee Type</button>}
              <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {types.map(t => (
                  <div key={t.id} className="bg-white rounded-lg shadow border p-4">
                    <h3 className="font-semibold text-gray-900">{t.name}</h3>
                    {t.description && <p className="text-xs text-gray-500 mt-1">{t.description}</p>}
                    <p className="text-lg font-bold text-indigo-600 mt-2">Rp {Number(t.amount).toLocaleString()}</p>
                    <span className="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{t.frequency}</span>
                  </div>
                ))}
                {types.length === 0 && !loading && <p className="col-span-full text-center text-gray-500 py-8">No fee types yet</p>}
              </div>
            </>
          ) : (
            <>
              {user?.role === 'admin' && (
                <button onClick={() => { setShowInvoiceForm(true); studentAPI.getList({ per_page: 50 }).then(r => setStudents(r.data?.data ?? [])).catch(() => {}); }} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ New Invoice</button>
              )}
              {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
                invoices.length === 0 ? <div className="text-center py-12 text-gray-500">No invoices</div> :
                <div className="bg-white rounded-lg shadow border overflow-hidden">
                  <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="bg-gray-50"><tr>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Student</th>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Fee Type</th>
                      <th className="px-4 py-3 text-right font-medium text-gray-500">Amount</th>
                      <th className="px-4 py-3 text-center font-medium text-gray-500">Due</th>
                      <th className="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                      <th className="px-4 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr></thead>
                    <tbody className="divide-y divide-gray-200">
                      {invoices.map(inv => {
                        const remaining = inv.amount - (inv.paid_amount ?? 0);
                        return (
                          <tr key={inv.id}>
                            <td className="px-4 py-3">{inv.student?.name}</td>
                            <td className="px-4 py-3">{inv.fee_type?.name}</td>
                            <td className="px-4 py-3 text-right">Rp {Number(inv.amount).toLocaleString()}</td>
                            <td className="px-4 py-3 text-center">{new Date(inv.due_date).toLocaleDateString()}</td>
                            <td className="px-4 py-3 text-center">
                              <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${inv.status === 'paid' ? 'bg-green-100 text-green-800' : inv.status === 'overdue' ? 'bg-red-100 text-red-800' : inv.status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'}`}>{inv.status}</span>
                            </td>
                            <td className="px-4 py-3 text-right">
                              {inv.status !== 'paid' && (user?.role === 'admin' || user?.role === 'student') && <button onClick={() => openPayForm(inv)} className="text-indigo-600 hover:text-indigo-800">Pay</button>}
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              }
            </>
          )}
        </div>

        {/* Type Form Modal */}
        {showTypeForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Add Fee Type</h2>
              <form onSubmit={handleTypeSave} className="space-y-4">
                <input type="text" value={typeForm.name} onChange={e => setTypeForm(p => ({ ...p, name: e.target.value }))} placeholder="Name" required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="grid grid-cols-2 gap-3">
                  <input type="number" value={typeForm.amount} onChange={e => setTypeForm(p => ({ ...p, amount: e.target.value }))} placeholder="Amount" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <select value={typeForm.frequency} onChange={e => setTypeForm(p => ({ ...p, frequency: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="once">Once</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option>
                  </select>
                </div>
                <textarea value={typeForm.description} onChange={e => setTypeForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowTypeForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Create</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Invoice Form Modal */}
        {showInvoiceForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">New Invoice</h2>
              <form onSubmit={handleInvoiceCreate} className="space-y-4">
                <select value={invoiceForm.student_id} onChange={e => setInvoiceForm(p => ({ ...p, student_id: e.target.value }))} required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                  <option value="">Select Student</option>
                  {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
                </select>
                <select value={invoiceForm.fee_type_id} onChange={e => { setInvoiceForm(p => ({ ...p, fee_type_id: e.target.value })); const t = types.find(t => t.id === Number(e.target.value)); if (t) setInvoiceForm(p => ({ ...p, amount: String(t.amount) })); }} required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                  <option value="">Select Fee Type</option>
                  {types.map(t => <option key={t.id} value={t.id}>{t.name} — Rp {Number(t.amount).toLocaleString()}</option>)}
                </select>
                <div className="grid grid-cols-2 gap-3">
                  <input type="number" value={invoiceForm.amount} onChange={e => setInvoiceForm(p => ({ ...p, amount: e.target.value }))} placeholder="Amount (auto)" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="date" value={invoiceForm.due_date} onChange={e => setInvoiceForm(p => ({ ...p, due_date: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <textarea value={invoiceForm.notes} onChange={e => setInvoiceForm(p => ({ ...p, notes: e.target.value }))} placeholder="Notes" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowInvoiceForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Create</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Pay Form Modal */}
        {showPayForm && selectedInvoice && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Pay Invoice</h2>
              <p className="text-sm text-gray-600 mb-4">{selectedInvoice.fee_type?.name} — Rp {Number(selectedInvoice.amount).toLocaleString()}</p>
              <form onSubmit={handlePay} className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <input type="number" value={payForm.amount} onChange={e => setPayForm(p => ({ ...p, amount: e.target.value }))} placeholder="Amount" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="date" value={payForm.payment_date} onChange={e => setPayForm(p => ({ ...p, payment_date: e.target.value }))} required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <select value={payForm.payment_method} onChange={e => setPayForm(p => ({ ...p, payment_method: e.target.value }))} className="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="cash">Cash</option><option value="transfer">Transfer</option><option value="cheque">Cheque</option><option value="other">Other</option>
                  </select>
                  <input type="text" value={payForm.reference_no} onChange={e => setPayForm(p => ({ ...p, reference_no: e.target.value }))} placeholder="Reference No" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <textarea value={payForm.notes} onChange={e => setPayForm(p => ({ ...p, notes: e.target.value }))} placeholder="Notes" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowPayForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Pay</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
