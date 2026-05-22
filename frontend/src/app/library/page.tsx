"use client";
import React, { useEffect, useState } from 'react';
import { libraryAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';

export default function LibraryPage() {
  const { toast } = useToast();
  const { user } = useAuth();
  const [tab, setTab] = useState<'books' | 'loans'>('books');
  const [books, setBooks] = useState<any[]>([]);
  const [loans, setLoans] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showBookForm, setShowBookForm] = useState(false);
  const [showLoanForm, setShowLoanForm] = useState(false);
  const [students, setStudents] = useState<any[]>([]);
  const [bookForm, setBookForm] = useState({ title: '', author: '', isbn: '', publisher: '', published_year: '', category: '', description: '', total_copies: 1, location: '' });
  const [loanForm, setLoanForm] = useState({ book_id: '', user_id: '', due_date: '', notes: '' });

  const fetchBooks = async () => {
    try { setLoading(true); const params: any = {}; if (search) params.search = search; const res = await libraryAPI.getBooks(params); setBooks(res.data?.data?.data ?? res.data?.data ?? []); }
    catch { /* */ } finally { setLoading(false); }
  };

  const fetchLoans = async () => {
    try { setLoading(true); const res = await libraryAPI.getLoans(); setLoans(res.data?.data?.data ?? res.data?.data ?? []); }
    catch { /* */ } finally { setLoading(false); }
  };

  useEffect(() => { tab === 'books' ? fetchBooks() : fetchLoans(); }, [tab]);
  useEffect(() => { if (showLoanForm) studentAPI.getList({ per_page: 50 }).then(r => setStudents(r.data?.data ?? [])).catch(() => {}); }, [showLoanForm]);

  const handleBookSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try { await libraryAPI.createBook({ ...bookForm, total_copies: Number(bookForm.total_copies), published_year: bookForm.published_year ? Number(bookForm.published_year) : null }); toast('Book added', 'success'); setShowBookForm(false); setBookForm({ title: '', author: '', isbn: '', publisher: '', published_year: '', category: '', description: '', total_copies: 1, location: '' }); fetchBooks(); }
    catch { toast('Failed to save', 'error'); }
  };

  const handleLoanCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try { await libraryAPI.createLoan({ book_id: Number(loanForm.book_id), user_id: Number(loanForm.user_id), due_date: loanForm.due_date, notes: loanForm.notes }); toast('Book loaned', 'success'); setShowLoanForm(false); setLoanForm({ book_id: '', user_id: '', due_date: '', notes: '' }); fetchLoans(); }
    catch { toast('Failed to loan', 'error'); }
  };

  const handleReturn = async (id: number) => {
    try { await libraryAPI.returnBook(id); toast('Book returned', 'success'); fetchLoans(); }
    catch { toast('Failed to return', 'error'); }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Library</h1>
            <div className="flex gap-2">
              <button onClick={() => setTab('books')} className={`px-3 py-2 text-sm rounded-md ${tab === 'books' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300'}`}>Books</button>
              <button onClick={() => setTab('loans')} className={`px-3 py-2 text-sm rounded-md ${tab === 'loans' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300'}`}>Loans</button>
            </div>
          </div>

          {tab === 'books' ? (
            <>
              <div className="flex gap-3">
                <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Search by title, author, or ISBN..." className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <button onClick={fetchBooks} className="px-3 py-2 text-sm border border-gray-300 rounded-md">Search</button>
                {user?.role !== 'student' && <button onClick={() => setShowBookForm(true)} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ Add Book</button>}
              </div>
              {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
                books.length === 0 ? <div className="text-center py-12 text-gray-500">No books found</div> :
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                  {books.map(b => (
                    <div key={b.id} className="bg-white rounded-lg shadow border p-4">
                      <h3 className="font-semibold text-gray-900">{b.title}</h3>
                      <p className="text-sm text-gray-500">{b.author}</p>
                      <p className="text-xs text-gray-400 mt-1">ISBN: {b.isbn}</p>
                      <div className="flex items-center justify-between mt-3 text-sm">
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${b.available_copies > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                          {b.available_copies}/{b.total_copies} available
                        </span>
                        {b.category && <span className="text-xs text-gray-400">{b.category}</span>}
                      </div>
                    </div>
                  ))}
                </div>
              }
            </>
          ) : (
            <>
              {user?.role !== 'student' && <button onClick={() => setShowLoanForm(true)} className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">+ New Loan</button>}
              {loading ? <div className="text-center py-12 text-gray-500">Loading...</div> :
                loans.length === 0 ? <div className="text-center py-12 text-gray-500">No loans</div> :
                <div className="bg-white rounded-lg shadow border overflow-hidden">
                  <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="bg-gray-50"><tr>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Book</th>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Borrower</th>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Loan Date</th>
                      <th className="px-4 py-3 text-left font-medium text-gray-500">Due Date</th>
                      <th className="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                      <th className="px-4 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr></thead>
                    <tbody className="divide-y divide-gray-200">
                      {loans.map(l => (
                        <tr key={l.id}>
                          <td className="px-4 py-3">{l.book?.title}</td>
                          <td className="px-4 py-3">{l.user?.name}</td>
                          <td className="px-4 py-3">{new Date(l.loan_date).toLocaleDateString()}</td>
                          <td className="px-4 py-3">{new Date(l.due_date).toLocaleDateString()}</td>
                          <td className="px-4 py-3 text-center">
                            <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${l.status === 'returned' ? 'bg-green-100 text-green-800' : l.status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}`}>{l.status}</span>
                          </td>
                          <td className="px-4 py-3 text-right">
                            {l.status !== 'returned' && user?.role !== 'student' && <button onClick={() => handleReturn(l.id)} className="text-indigo-600 hover:text-indigo-800">Return</button>}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              }
            </>
          )}
        </div>

        {/* Book Form Modal */}
        {showBookForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6 max-h-[80vh] overflow-y-auto">
              <h2 className="text-lg font-semibold mb-4">Add Book</h2>
              <form onSubmit={handleBookSave} className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={bookForm.title} onChange={e => setBookForm(p => ({ ...p, title: e.target.value }))} placeholder="Title" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.author} onChange={e => setBookForm(p => ({ ...p, author: e.target.value }))} placeholder="Author" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <input type="text" value={bookForm.isbn} onChange={e => setBookForm(p => ({ ...p, isbn: e.target.value }))} placeholder="ISBN" required className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.publisher} onChange={e => setBookForm(p => ({ ...p, publisher: e.target.value }))} placeholder="Publisher" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-3 gap-3">
                  <input type="number" value={bookForm.published_year} onChange={e => setBookForm(p => ({ ...p, published_year: e.target.value }))} placeholder="Year" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.category} onChange={e => setBookForm(p => ({ ...p, category: e.target.value }))} placeholder="Category" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                  <input type="number" value={bookForm.total_copies} onChange={e => setBookForm(p => ({ ...p, total_copies: Number(e.target.value) }))} placeholder="Copies" min={1} className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <textarea value={bookForm.description} onChange={e => setBookForm(p => ({ ...p, description: e.target.value }))} placeholder="Description" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <input type="text" value={bookForm.location} onChange={e => setBookForm(p => ({ ...p, location: e.target.value }))} placeholder="Shelf location" className="rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowBookForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Add</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Loan Form Modal */}
        {showLoanForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
              <h2 className="text-lg font-semibold mb-4">New Loan</h2>
              <form onSubmit={handleLoanCreate} className="space-y-4">
                <select value={loanForm.book_id} onChange={e => setLoanForm(p => ({ ...p, book_id: e.target.value }))} required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                  <option value="">Select Book</option>
                  {books.filter(b => b.available_copies > 0).map(b => <option key={b.id} value={b.id}>{b.title} ({b.available_copies} avail)</option>)}
                </select>
                <select value={loanForm.user_id} onChange={e => setLoanForm(p => ({ ...p, user_id: e.target.value }))} required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                  <option value="">Select Student</option>
                  {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
                </select>
                <input type="date" value={loanForm.due_date} onChange={e => setLoanForm(p => ({ ...p, due_date: e.target.value }))} required className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <textarea value={loanForm.notes} onChange={e => setLoanForm(p => ({ ...p, notes: e.target.value }))} placeholder="Notes" rows={2} className="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowLoanForm(false)} className="px-4 py-2 text-sm border border-gray-300 rounded-md">Cancel</button>
                  <button type="submit" className="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md">Loan</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
