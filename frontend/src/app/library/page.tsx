"use client";
import React, { useEffect, useState } from 'react';
import { libraryAPI, studentAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input, Select } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

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

  const availVariant = (b: any) => b.available_copies > 0 ? 'success' as const : 'danger' as const;
  const loanStatusVariant = (s: string) => {
    if (s === 'returned') return 'success' as const;
    if (s === 'overdue') return 'danger' as const;
    return 'warning' as const;
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Perpustakaan"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Perpustakaan' }]}
            action={
              <div className="flex flex-wrap gap-2">
                <button onClick={() => setTab('books')} className={`px-3 py-2 text-sm rounded-md ${tab === 'books' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Buku</button>
                <button onClick={() => setTab('loans')} className={`px-3 py-2 text-sm rounded-md ${tab === 'loans' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' : 'bg-card border border-border'}`}>Peminjaman</button>
              </div>
            }
          />

          {tab === 'books' ? (
            <>
              <div className="flex flex-wrap items-center gap-3">
                <Input
                  placeholder="Cari judul, penulis, atau ISBN..."
                  value={search}
                  onChange={e => setSearch(e.target.value)}
                  icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>}
                />
                <Button variant="secondary" size="sm" onClick={fetchBooks}>Cari</Button>
                {user?.role !== 'student' && <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>} onClick={() => setShowBookForm(true)}>Tambah Buku</Button>}
              </div>
              {loading ? <div className="text-center py-12 text-muted-foreground">Memuat...</div> :
                books.length === 0 ? <div className="text-center py-12 text-muted-foreground">Tidak ada buku</div> :
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                  {books.map(b => (
                    <div key={b.id} className="rounded-xl border bg-card text-card-foreground shadow-sm border p-4">
                      <h3 className="font-semibold text-foreground">{b.title}</h3>
                      <p className="text-sm text-muted-foreground">{b.author}</p>
                      <p className="text-xs text-muted-foreground/60 mt-1">ISBN: {b.isbn}</p>
                      <div className="flex items-center justify-between mt-3 text-sm">
                        <Badge variant={availVariant(b)}>{b.available_copies}/{b.total_copies} tersedia</Badge>
                        {b.category && <span className="text-xs text-muted-foreground/60">{b.category}</span>}
                      </div>
                    </div>
                  ))}
                </div>
              }
            </>
          ) : (
            <>
              {user?.role !== 'student' && <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>} onClick={() => setShowLoanForm(true)}>Peminjaman Baru</Button>}
              <DataTable
                columns={[
                  { key: 'book', label: 'Buku', render: (row) => row.book?.title },
                  { key: 'user', label: 'Peminjam', render: (row) => row.user?.name },
                  { key: 'loan_date', label: 'Tanggal Pinjam', render: (row) => new Date(row.loan_date).toLocaleDateString() },
                  { key: 'due_date', label: 'Jatuh Tempo', render: (row) => new Date(row.due_date).toLocaleDateString() },
                  { key: 'status', label: 'Status', render: (row) => <Badge variant={loanStatusVariant(row.status)}>{row.status}</Badge> },
                  { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                    row.status !== 'returned' && user?.role !== 'student' ? (
                      <Button variant="ghost" size="sm" onClick={() => handleReturn(row.id)}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                        Kembali
                      </Button>
                    ) : null
                  )},
                ]}
                data={loans}
                keyExtractor={(row) => row.id}
                loading={loading}
                emptyMessage="Belum ada peminjaman."
              />
            </>
          )}
        </div>

        {showBookForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6 max-h-[80vh] overflow-y-auto">
              <h2 className="text-lg font-semibold mb-4">Tambah Buku</h2>
              <form onSubmit={handleBookSave} className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input type="text" value={bookForm.title} onChange={e => setBookForm(p => ({ ...p, title: e.target.value }))} placeholder="Judul" required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.author} onChange={e => setBookForm(p => ({ ...p, author: e.target.value }))} placeholder="Penulis" required className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input type="text" value={bookForm.isbn} onChange={e => setBookForm(p => ({ ...p, isbn: e.target.value }))} placeholder="ISBN" required className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.publisher} onChange={e => setBookForm(p => ({ ...p, publisher: e.target.value }))} placeholder="Penerbit" className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <input type="number" value={bookForm.published_year} onChange={e => setBookForm(p => ({ ...p, published_year: e.target.value }))} placeholder="Tahun" className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="text" value={bookForm.category} onChange={e => setBookForm(p => ({ ...p, category: e.target.value }))} placeholder="Kategori" className="rounded-md border border-border px-3 py-2 text-sm" />
                  <input type="number" value={bookForm.total_copies} onChange={e => setBookForm(p => ({ ...p, total_copies: Number(e.target.value) }))} placeholder="Jumlah" min={1} className="rounded-md border border-border px-3 py-2 text-sm" />
                </div>
                <textarea value={bookForm.description} onChange={e => setBookForm(p => ({ ...p, description: e.target.value }))} placeholder="Deskripsi" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <input type="text" value={bookForm.location} onChange={e => setBookForm(p => ({ ...p, location: e.target.value }))} placeholder="Lokasi Rak" className="rounded-md border border-border px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowBookForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Batal</button>
                  <Button type="submit">Tambah</Button>
                </div>
              </form>
            </div>
          </div>
        )}

        {showLoanForm && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-xl border bg-card text-card-foreground shadow-sm-lg p-6">
              <h2 className="text-lg font-semibold mb-4">Peminjaman Baru</h2>
              <form onSubmit={handleLoanCreate} className="space-y-4">
                <select value={loanForm.book_id} onChange={e => setLoanForm(p => ({ ...p, book_id: e.target.value }))} required className="block w-full rounded-md border border-border px-3 py-2 text-sm">
                  <option value="">Pilih Buku</option>
                  {books.filter(b => b.available_copies > 0).map(b => <option key={b.id} value={b.id}>{b.title} ({b.available_copies} tersedia)</option>)}
                </select>
                <select value={loanForm.user_id} onChange={e => setLoanForm(p => ({ ...p, user_id: e.target.value }))} required className="block w-full rounded-md border border-border px-3 py-2 text-sm">
                  <option value="">Pilih Siswa</option>
                  {students.map((s: any) => <option key={s.id} value={s.id}>{s.name} — {s.email}</option>)}
                </select>
                <input type="date" value={loanForm.due_date} onChange={e => setLoanForm(p => ({ ...p, due_date: e.target.value }))} required className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <textarea value={loanForm.notes} onChange={e => setLoanForm(p => ({ ...p, notes: e.target.value }))} placeholder="Catatan" rows={2} className="block w-full rounded-md border border-border px-3 py-2 text-sm" />
                <div className="flex justify-end gap-3">
                  <button type="button" onClick={() => setShowLoanForm(false)} className="px-4 py-2 text-sm border border-border rounded-md">Batal</button>
                  <Button type="submit">Pinjam</Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </MainLayout>
    </ProtectedRoute>
  );
}
