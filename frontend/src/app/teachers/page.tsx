"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { teacherAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { TeacherFormModal } from '@/components/teachers/TeacherFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

interface Teacher {
  id: number; name: string; email: string; status: string;
  gender?: string; phone?: string; date_of_birth?: string;
}

interface Pagination { total: number; per_page: number; current_page: number; last_page: number; from: number | null; to: number | null; }

export default function TeachersPage() {
  const { toast } = useToast();
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Teacher | undefined>();
  const [deleteTarget, setDeleteTarget] = useState<Teacher | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fetch = useCallback(async (query?: string, pageNum = 1) => {
    try {
      setLoading(true);
      const params: Record<string, string | number> = { per_page: perPage, page: pageNum };
      if (query) params.name = query;
      const res = await teacherAPI.getPaginated(params);
      const body = res.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      if (Array.isArray(body?.data)) {
        setTeachers(body.data as Teacher[]);
        if (body.pagination) setPagination(body.pagination);
      } else { setTeachers([]); setPagination(null); }
    } catch { toast('Failed to load teachers', 'error'); }
    finally { setLoading(false); }
  }, [toast, perPage]);

  useEffect(() => { fetch(search, page); }, [page, perPage]);
  const handleSearch = (e: React.FormEvent) => { e.preventDefault(); setPage(1); fetch(search, 1); };
  const handlePerPage = (val: string) => { setPerPage(Number(val)); setPage(1); };

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try { await teacherAPI.delete(String(deleteTarget.id)); toast('Teacher deleted', 'success'); setDeleteTarget(null); fetch(search, page); }
    catch { toast('Failed to delete', 'error'); }
    finally { setDeleting(false); }
  };

  return (
    <ProtectedRoute roles={['admin']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Teachers</h1>
              {pagination && <p className="text-sm text-gray-500 mt-1">Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}</p>}
            </div>
            <div className="flex items-center gap-2">
              <button onClick={() => exportAPI.download('teachers')} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Export CSV</button>
              <button onClick={() => { setEditing(undefined); setFormOpen(true); }} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Teacher</button>
            </div>
          </div>
          <form onSubmit={handleSearch} className="flex gap-2">
            <input type="text" placeholder="Search by name..." className="flex-1 rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value={search} onChange={(e) => setSearch(e.target.value)} />
            <button type="submit" className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Search</button>
            {search && <button type="button" onClick={() => { setSearch(''); setPage(1); fetch('', 1); }} className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Clear</button>}
          </form>
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading teachers...</div>
          ) : teachers.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No teachers found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {teachers.map((t) => (
                    <tr key={t.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{t.name}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{t.email}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{t.gender || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${t.status === 'active' ? 'bg-green-100 text-green-800' : t.status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}`}>{t.status}</span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <button onClick={() => { setEditing(t); setFormOpen(true); }} className="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                        <button onClick={() => setDeleteTarget(t)} className="text-red-600 hover:text-red-900">Delete</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
          {pagination && pagination.last_page > 1 && (
            <div className="flex items-center justify-between gap-4">
              <div className="flex items-center gap-2 text-sm text-gray-600">
                <span>Rows:</span>
                <select className="rounded border border-gray-300 px-2 py-1 text-sm" value={perPage} onChange={(e) => handlePerPage(e.target.value)}>
                  <option value={10}>10</option>
                  <option value={25}>25</option>
                  <option value={50}>50</option>
                  <option value={100}>100</option>
                </select>
              </div>
              <div className="flex items-center gap-2">
              <button onClick={() => setPage((p) => Math.max(1, p - 1))} disabled={page <= 1} className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50">Prev</button>
              {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((n) => (
                <button key={n} onClick={() => setPage(n)} className={`px-3 py-1.5 text-sm font-medium rounded-md ${n === page ? 'bg-indigo-600 text-white' : 'border border-gray-300 hover:bg-gray-50'}`}>{n}</button>
              ))}
              <button onClick={() => setPage((p) => Math.min(pagination.last_page, p + 1))} disabled={page >= pagination.last_page} className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50">Next</button>
            </div>
            </div>
          )}
        </div>
        <TeacherFormModal open={formOpen} onClose={() => setFormOpen(false)} onSuccess={(msg) => { toast(msg, 'success'); fetch(search, page); }} teacher={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Delete Teacher" message={`Delete ${deleteTarget?.name}? This cannot be undone.`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
