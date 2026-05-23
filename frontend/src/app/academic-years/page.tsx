"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { academicYearAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { AcademicYearFormModal } from '@/components/academic-years/AcademicYearFormModal';
import { TermFormModal } from '@/components/academic-years/TermFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';
import { PageHeader } from '@/components/ui/PageHeader';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';

interface AcademicYear {
  id: number; name: string; start_date: string; end_date: string; is_active: boolean;
  terms?: Term[];
}

interface Term {
  id: number; academic_year_id: number; name: string; start_date: string; end_date: string; is_active: boolean;
}

export default function AcademicYearsPage() {
  const { toast } = useToast();
  const [years, setYears] = useState<AcademicYear[]>([]);
  const [loading, setLoading] = useState(true);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<AcademicYear | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<AcademicYear | null>(null);
  const [deleting, setDeleting] = useState(false);
  const [expandedId, setExpandedId] = useState<number | null>(null);

  const [termFormOpen, setTermFormOpen] = useState(false);
  const [termAcademicYearId, setTermAcademicYearId] = useState<number>(0);
  const [termEditing, setTermEditing] = useState<Term | null>(null);
  const [deleteTermTarget, setDeleteTermTarget] = useState<Term | null>(null);
  const [deletingTerm, setDeletingTerm] = useState(false);

  const fetch = useCallback(async () => {
    try { setLoading(true); const res = await academicYearAPI.getList(); setYears(res.data?.data ?? []); }
    catch { toast('Failed to load academic years', 'error'); }
    finally { setLoading(false); }
  }, [toast]);

  useEffect(() => { fetch(); }, []);

  const handleDelete = async () => {
    if (!deleteTarget) return;
    setDeleting(true);
    try { await academicYearAPI.delete(deleteTarget.id); toast('Academic year deleted', 'success'); setDeleteTarget(null); fetch(); }
    catch { toast('Failed to delete', 'error'); }
    finally { setDeleting(false); }
  };

  const handleDeleteTerm = async () => {
    if (!deleteTermTarget) return;
    setDeletingTerm(true);
    try { await academicYearAPI.deleteTerm(deleteTermTarget.id); toast('Term deleted', 'success'); setDeleteTermTarget(null); fetch(); }
    catch { toast('Failed to delete', 'error'); }
    finally { setDeletingTerm(false); }
  };

  const openTermForm = (yearId: number, term?: Term) => {
    setTermAcademicYearId(yearId);
    setTermEditing(term ?? null);
    setTermFormOpen(true);
  };

  return (
    <ProtectedRoute roles={['admin']}>
      <MainLayout>
        <div className="space-y-6">
          <PageHeader
            title="Tahun Ajaran"
            breadcrumbs={[{ label: 'Dashboard', href: '/dashboard' }, { label: 'Tahun Ajaran' }]}
            action={
              <Button size="sm" icon={<svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>}
                onClick={() => { setEditing(null); setFormOpen(true); }}
              >
                Tambah
              </Button>
            }
          />

          {loading ? (
            <div className="text-center py-12 text-muted-foreground">Memuat...</div>
          ) : years.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">Belum ada tahun ajaran.</div>
          ) : (
            <div className="space-y-4">
              {years.map(year => (
                <div key={year.id} className="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                  <div className="px-6 py-4 flex items-center justify-between hover:bg-muted/50 cursor-pointer"
                    onClick={() => setExpandedId(expandedId === year.id ? null : year.id)}>
                    <div className="flex items-center gap-3">
                      <Badge variant={year.is_active ? 'success' : 'default'}>{year.is_active ? 'Aktif' : 'Tidak Aktif'}</Badge>
                      <div>
                        <p className="font-medium text-foreground">{year.name}</p>
                        <p className="text-sm text-muted-foreground">{year.start_date} – {year.end_date}</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-2" onClick={e => e.stopPropagation()}>
                      <Button variant="ghost" size="sm" onClick={() => { setEditing(year); setFormOpen(true); }}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                      </Button>
                      <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(year)}>
                        <svg className="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                      </Button>
                    </div>
                  </div>

                  {expandedId === year.id && (
                    <div className="border-t border-border px-6 py-4">
                      <div className="flex items-center justify-between mb-3">
                        <h3 className="text-sm font-semibold text-foreground/80">Semester</h3>
                        <Button variant="ghost" size="sm" onClick={() => openTermForm(year.id)}>
                          <svg className="size-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                          Tambah Semester
                        </Button>
                      </div>
                      {(!year.terms || year.terms.length === 0) ? (
                        <p className="text-sm text-muted-foreground">Belum ada semester untuk tahun ajaran ini.</p>
                      ) : (
                        <table className="min-w-full divide-y divide-border text-sm">
                          <thead><tr className="bg-muted/50">
                            <th className="px-4 py-2 text-left font-medium text-muted-foreground">Nama</th>
                            <th className="px-4 py-2 text-left font-medium text-muted-foreground">Periode</th>
                            <th className="px-4 py-2 text-center font-medium text-muted-foreground">Status</th>
                            <th className="px-4 py-2 text-right font-medium text-muted-foreground">Aksi</th>
                          </tr></thead>
                          <tbody className="divide-y divide-border">
                            {year.terms.map(term => (
                              <tr key={term.id}>
                                <td className="px-4 py-2">{term.name}</td>
                                <td className="px-4 py-2 text-muted-foreground">{term.start_date} – {term.end_date}</td>
                                <td className="px-4 py-2 text-center">
                                  <Badge variant={term.is_active ? 'success' : 'default'}>{term.is_active ? 'Aktif' : 'Tidak Aktif'}</Badge>
                                </td>
                                <td className="px-4 py-2 text-right">
                                  <Button variant="ghost" size="sm" onClick={() => openTermForm(year.id, term)}>
                                    <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                  </Button>
                                  <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => setDeleteTermTarget(term)}>
                                    <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                  </Button>
                                </td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      )}
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>

        <AcademicYearFormModal open={formOpen} onClose={() => setFormOpen(false)} onSuccess={fetch} editing={editing} />
        <TermFormModal open={termFormOpen} onClose={() => setTermFormOpen(false)} onSuccess={fetch} academicYearId={termAcademicYearId} editing={termEditing} />
        <ConfirmDialog open={!!deleteTarget} title="Hapus Tahun Ajaran" message="Ini akan menghapus semua semester di tahun ini. Yakin?" loading={deleting} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} />
        <ConfirmDialog open={!!deleteTermTarget} title="Hapus Semester" message="Yakin ingin menghapus semester ini?" loading={deletingTerm} onConfirm={handleDeleteTerm} onCancel={() => setDeleteTermTarget(null)} />
      </MainLayout>
    </ProtectedRoute>
  );
}
