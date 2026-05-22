"use client";
import React, { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { studentAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { StudentFormModal } from '@/components/students/StudentFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

interface Student {
  id: number;
  name: string;
  email: string;
  nisn?: string;
  kelas_id?: number | null;
  kelas?: { id: number; name: string } | null;
  status: string;
  gender?: string;
  phone?: string;
  address?: string;
  date_of_birth?: string;
}

interface Pagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
  from: number | null;
  to: number | null;
}

export default function StudentsPage() {
  const { toast } = useToast();

  const [students, setStudents] = useState<Student[]>([]);
  const [pagination, setPagination] = useState<Pagination | null>(null);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [filterKelas, setFilterKelas] = useState('');
  const [filterStatus, setFilterStatus] = useState('');
  const [classes, setClasses] = useState<{ id: number; name: string }[]>([]);

  useEffect(() => {
    (async () => {
      try {
        const { classAPI } = await import('@/lib/api');
        const res = await classAPI.getList();
        const body = res.data as { success?: boolean; data?: unknown[] };
        if (Array.isArray(body?.data)) setClasses(body.data as { id: number; name: string }[]);
      } catch { /* */ }
    })();
  }, []);

  // Modal state
  const [formOpen, setFormOpen] = useState(false);
  const [editingStudent, setEditingStudent] = useState<Student | undefined>();

  // Delete state
  const [deleteTarget, setDeleteTarget] = useState<Student | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fetchStudents = useCallback(async (query?: string, pageNum = 1, kelasId = '', status = '') => {
    try {
      setLoading(true);
      const params: Record<string, string | number> = { per_page: perPage, page: pageNum };
      if (query) params.name = query;
      if (kelasId) params.kelas_id = kelasId;
      if (status) params.status = status;
      const response = await studentAPI.getPaginated(params);
      const body = response.data as { success?: boolean; data?: unknown[]; pagination?: Pagination };
      const items = body?.data;
      if (Array.isArray(items)) {
        setStudents(items as Student[]);
        if (body.pagination) {
          setPagination(body.pagination);
        }
      } else {
        setStudents([]);
        setPagination(null);
      }
    } catch (err) {
      toast('Failed to load students', 'error');
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, [toast, perPage]);

  useEffect(() => {
    fetchStudents(search, page, filterKelas, filterStatus);
  }, [page, filterKelas, filterStatus, perPage]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setPage(1);
    fetchStudents(search, 1, filterKelas, filterStatus);
  };

  const handlePerPageChange = (val: string) => {
    setPerPage(Number(val));
    setPage(1);
  };

  const openCreate = () => {
    setEditingStudent(undefined);
    setFormOpen(true);
  };

  const openEdit = (student: Student) => {
    setEditingStudent(student);
    setFormOpen(true);
  };

  const handleFormSuccess = (message: string) => {
    toast(message, 'success');
    fetchStudents(search, page, filterKelas, filterStatus);
  };

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try {
      await studentAPI.delete(String(deleteTarget.id));
      toast('Student deleted successfully', 'success');
      setDeleteTarget(null);
      fetchStudents(search, page, filterKelas, filterStatus);
    } catch (err) {
      toast('Failed to delete student', 'error');
      console.error(err);
    } finally {
      setDeleting(false);
    }
  };

  return (
    <ProtectedRoute roles={['admin', 'teacher']}>
      <MainLayout>
        <div className="space-y-6">
          {/* Header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Students</h1>
              {pagination && (
                <p className="text-sm text-gray-500 mt-1">
                  Showing {pagination.from ?? 0}–{pagination.to ?? 0} of {pagination.total}
                </p>
              )}
            </div>
            <div className="flex items-center gap-2">
              <button onClick={() => exportAPI.download('students')} className="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Export CSV</button>
              <button
                onClick={openCreate}
                className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors"
              >
                + Add Student
              </button>
            </div>
          </div>

          {/* Filters */}
          <div className="flex flex-wrap items-center gap-3">
            <input
              type="text"
              placeholder="Search by name..."
              className="flex-1 min-w-[200px] rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSearch(e as unknown as React.FormEvent)}
            />
            <select
              className="rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
              value={filterKelas}
              onChange={(e) => { setFilterKelas(e.target.value); setPage(1); }}
            >
              <option value="">All Classes</option>
              {classes.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
            </select>
            <select
              className="rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
              value={filterStatus}
              onChange={(e) => { setFilterStatus(e.target.value); setPage(1); }}
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
            </select>
            <button
              type="button"
              onClick={() => { setSearch(''); setFilterKelas(''); setFilterStatus(''); setPage(1); }}
              className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
            >
              Clear
            </button>
          </div>

          {/* Table */}
          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading students...</div>
          ) : students.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No students found.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {students.map((student) => (
                    <tr key={student.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{student.name}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{student.nisn || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{student.email}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{student.kelas?.name || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{student.gender || '—'}</td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                          student.status === 'active'
                            ? 'bg-green-100 text-green-800'
                            : student.status === 'inactive'
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-red-100 text-red-800'
                        }`}>
                          {student.status}
                        </span>
                      </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right">
                      <Link
                        href={`/profile?id=${student.id}`}
                        className="text-gray-600 hover:text-gray-900 mr-4"
                      >
                        Profile
                      </Link>
                      <button
                        onClick={() => openEdit(student)}
                        className="text-indigo-600 hover:text-indigo-900 mr-4"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => setDeleteTarget(student)}
                        className="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {/* Pagination */}
          {pagination && pagination.last_page > 1 && (
            <div className="flex items-center justify-between gap-4">
              <div className="flex items-center gap-2 text-sm text-gray-600">
                <span>Rows:</span>
                <select
                  className="rounded border border-gray-300 px-2 py-1 text-sm"
                  value={perPage}
                  onChange={(e) => handlePerPageChange(e.target.value)}
                >
                  <option value={10}>10</option>
                  <option value={25}>25</option>
                  <option value={50}>50</option>
                  <option value={100}>100</option>
                </select>
              </div>
              <div className="flex items-center gap-2">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page <= 1}
                className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50"
              >
                Prev
              </button>
              {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((n) => (
                <button
                  key={n}
                  onClick={() => setPage(n)}
                  className={`px-3 py-1.5 text-sm font-medium rounded-md ${
                    n === page
                      ? 'bg-indigo-600 text-white'
                      : 'border border-gray-300 hover:bg-gray-50'
                  }`}
                >
                  {n}
                </button>
              ))}
              <button
                onClick={() => setPage((p) => Math.min(pagination.last_page, p + 1))}
                disabled={page >= pagination.last_page}
                className="px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 disabled:opacity-40 hover:bg-gray-50"
              >
                Next
              </button>
            </div>
            </div>
          )}
        </div>

        {/* Form Modal */}
        <StudentFormModal
          open={formOpen}
          onClose={() => setFormOpen(false)}
          onSuccess={handleFormSuccess}
          student={editingStudent}
        />

        {/* Delete Confirmation */}
        <ConfirmDialog
          open={!!deleteTarget}
          title="Delete Student"
          message={`Are you sure you want to delete ${deleteTarget?.name}? This action cannot be undone.`}
          onConfirm={handleDelete}
          onCancel={() => setDeleteTarget(null)}
          loading={deleting}
        />
      </MainLayout>
    </ProtectedRoute>
  );
}

interface Pagination {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
  from: number | null;
  to: number | null;
}
