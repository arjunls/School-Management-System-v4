"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { classAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { ClassFormModal } from '@/components/classes/ClassFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

interface ClassItem {
  id: number; name: string; grade_level: number; capacity: number;
  homeroom_teacher_id?: number | null; student_count?: number;
}

interface Pagination { total: number; per_page: number; current_page: number; last_page: number; from: number | null; to: number | null; }

export default function ClassesPage() {
  const { toast } = useToast();
  const [classes, setClasses] = useState<ClassItem[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<ClassItem | undefined>();
  const [deleteTarget, setDeleteTarget] = useState<ClassItem | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fetch = useCallback(async (pageNum = 1) => {
    try {
      setLoading(true);
      const res = await classAPI.getPaginated({ per_page: perPage, page: pageNum });
      const body = res.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      if (Array.isArray(body?.data)) { setClasses(body.data as ClassItem[]); if (body.pagination) setPagination(body.pagination); }
      else { setClasses([]); setPagination(null); }
    } catch { toast('Failed to load classes', 'error'); }
    finally { setLoading(false); }
  }, [toast, perPage]);

  useEffect(() => { fetch(page); }, [page, perPage]);

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try { await classAPI.delete(String(deleteTarget.id)); toast('Class deleted', 'success'); setDeleteTarget(null); fetch(page); }
    catch { toast('Failed to delete', 'error'); }
    finally { setDeleting(false); }
  };

  return (
    <ProtectedRoute roles={['admin']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Classes</h1>
              {pagination && <p className="text-sm text-gray-500 mt-1">Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}</p>}
            </div>
            <div className="flex items-center gap-2">
              <button onClick={() => exportAPI.download('classes')} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Export CSV</button>
              <button onClick={() => { setEditing(undefined); setFormOpen(true); }} className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Class</button>
            </div>
          </div>
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading classes...</div>
          ) : classes.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No classes found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {classes.map((c) => (
                    <tr key={c.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{c.name}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Grade {c.grade_level}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{c.capacity}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{c.student_count ?? '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <button onClick={() => { setEditing(c); setFormOpen(true); }} className="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                        <button onClick={() => setDeleteTarget(c)} className="text-red-600 hover:text-red-900">Delete</button>
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
                <select className="rounded border border-gray-300 px-2 py-1 text-sm" value={perPage} onChange={(e) => { setPerPage(Number(e.target.value)); setPage(1); }}>
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
        <ClassFormModal open={formOpen} onClose={() => setFormOpen(false)} onSuccess={(msg) => { toast(msg, 'success'); fetch(page); }} classData={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Delete Class" message={`Delete ${deleteTarget?.name}? This cannot be undone.`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
