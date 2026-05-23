"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { teacherAPI, exportAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { TeacherFormModal } from '@/components/teachers/TeacherFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';
import { DataTable } from '@/components/ui/DataTable';

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

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try { await teacherAPI.delete(String(deleteTarget.id)); toast('Teacher deleted', 'success'); setDeleteTarget(null); fetch(search, page); }
    catch { toast('Failed to delete', 'error'); }
    finally { setDeleting(false); }
  };

  const statusVariant = (s: string) => s === 'active' ? 'success' as const : s === 'inactive' ? 'warning' as const : 'danger' as const;

  return (
    <ProtectedRoute roles={['admin']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Guru"
            description={pagination ? `Menampilkan ${pagination.from ?? 0}-${pagination.to ?? 0} dari ${pagination.total}` : undefined}
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Guru' }]}
            action={
              <div className="flex items-center gap-2">
                <Button variant="secondary" size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>}
                  onClick={() => exportAPI.download('teachers')}
                >
                  Export
                </Button>
                <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                  onClick={() => { setEditing(undefined); setFormOpen(true); }}
                >
                  Tambah
                </Button>
              </div>
            }
          />

          <form onSubmit={handleSearch}>
            <div className="flex flex-wrap items-center gap-3">
              <Input
                placeholder="Cari nama..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                icon={<svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>}
              />
              <Button type="submit" size="sm">Cari</Button>
              {search && <Button variant="ghost" size="sm" onClick={() => { setSearch(''); setPage(1); fetch('', 1); }}>Reset</Button>}
            </div>
          </form>

          <DataTable
            columns={[
              { key: 'name', label: 'Nama', sortable: true },
              { key: 'email', label: 'Email', sortable: true },
              { key: 'gender', label: 'Jenis Kelamin', render: (row) => row.gender ? (row.gender.charAt(0).toUpperCase() + row.gender.slice(1)) : '—' },
              { key: 'status', label: 'Status', render: (row) => <Badge variant={statusVariant(row.status)}>{row.status}</Badge> },
              { key: 'id', label: 'Aksi', className: 'text-right', render: (row) => (
                <div className="flex justify-end gap-1">
                  <Button variant="ghost" size="sm" onClick={() => { setEditing(row); setFormOpen(true); }}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                  </Button>
                  <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(row)}>
                    <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                  </Button>
                </div>
              )},
            ]}
            data={teachers}
            keyExtractor={(row) => row.id}
            loading={loading}
            emptyMessage="Belum ada data guru."
            pageSize={perPage}
          />
        </div>
        <TeacherFormModal open={formOpen} onClose={() => setFormOpen(false)} onSuccess={(msg) => { toast(msg, 'success'); fetch(search, page); }} teacher={editing} />
        <ConfirmDialog open={!!deleteTarget} title="Hapus Guru" message={`Hapus ${deleteTarget?.name}? Tindakan ini tidak bisa dibatalkan.`} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} loading={deleting} />
      </MainLayout>
    </ProtectedRoute>
  );
}
