"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { academicYearAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { AcademicYearFormModal } from '@/components/academic-years/AcademicYearFormModal';
import { TermFormModal } from '@/components/academic-years/TermFormModal';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { useToast } from '@/components/ui/Toast';

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

  // Term modals
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
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Academic Years</h1>
            <button onClick={() => { setEditing(null); setFormOpen(true); }}
              className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">+ Add Academic Year</button>
          </div>

          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading...</div>
          ) : years.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No academic years found.</div>
          ) : (
            <div className="space-y-4">
              {years.map(year => (
                <div key={year.id} className="bg-white rounded-lg shadow overflow-hidden">
                  <div className="px-6 py-4 flex items-center justify-between hover:bg-gray-50 cursor-pointer"
                    onClick={() => setExpandedId(expandedId === year.id ? null : year.id)}>
                    <div className="flex items-center gap-3">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${year.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                        {year.is_active ? 'Active' : 'Inactive'}
                      </span>
                      <div>
                        <p className="font-medium text-gray-900">{year.name}</p>
                        <p className="text-sm text-gray-500">{year.start_date} – {year.end_date}</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-2" onClick={e => e.stopPropagation()}>
                      <button onClick={() => { setEditing(year); setFormOpen(true); }} className="text-sm text-indigo-600 hover:text-indigo-800">Edit</button>
                      <button onClick={() => setDeleteTarget(year)} className="text-sm text-red-600 hover:text-red-800">Delete</button>
                    </div>
                  </div>

                  {expandedId === year.id && (
                    <div className="border-t border-gray-200 px-6 py-4">
                      <div className="flex items-center justify-between mb-3">
                        <h3 className="text-sm font-semibold text-gray-700">Terms</h3>
                        <button onClick={() => openTermForm(year.id)}
                          className="text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add Term</button>
                      </div>
                      {(!year.terms || year.terms.length === 0) ? (
                        <p className="text-sm text-gray-500">No terms for this academic year.</p>
                      ) : (
                        <table className="min-w-full divide-y divide-gray-200 text-sm">
                          <thead><tr className="bg-gray-50">
                            <th className="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                            <th className="px-4 py-2 text-left font-medium text-gray-500">Period</th>
                            <th className="px-4 py-2 text-center font-medium text-gray-500">Status</th>
                            <th className="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                          </tr></thead>
                          <tbody className="divide-y divide-gray-200">
                            {year.terms.map(term => (
                              <tr key={term.id}>
                                <td className="px-4 py-2">{term.name}</td>
                                <td className="px-4 py-2 text-gray-500">{term.start_date} – {term.end_date}</td>
                                <td className="px-4 py-2 text-center">
                                  <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${term.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                                    {term.is_active ? 'Active' : 'Inactive'}
                                  </span>
                                </td>
                                <td className="px-4 py-2 text-right">
                                  <button onClick={() => openTermForm(year.id, term)} className="text-indigo-600 hover:text-indigo-800 mr-2">Edit</button>
                                  <button onClick={() => setDeleteTermTarget(term)} className="text-red-600 hover:text-red-800">Delete</button>
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
        <ConfirmDialog open={!!deleteTarget} title="Delete Academic Year" message="This will also delete all terms under this year. Are you sure?" loading={deleting} onConfirm={handleDelete} onCancel={() => setDeleteTarget(null)} />
        <ConfirmDialog open={!!deleteTermTarget} title="Delete Term" message="Are you sure you want to delete this term?" loading={deletingTerm} onConfirm={handleDeleteTerm} onCancel={() => setDeleteTermTarget(null)} />
      </MainLayout>
    </ProtectedRoute>
  );
}
