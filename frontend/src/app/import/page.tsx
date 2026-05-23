"use client";
import React, { useState, useRef } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';
import { useToast } from '@/components/ui/Toast';

export default function ImportPage() {
  const { toast } = useToast();
  const [type, setType] = useState<'students' | 'teachers'>('students');
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<string[][]>([]);
  const [importing, setImporting] = useState(false);
  const [result, setResult] = useState<{ created: number; errors: string[] } | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);

  const handleFile = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];
    if (!f) return;
    setFile(f);
    setResult(null);

    const reader = new FileReader();
    reader.onload = (ev) => {
      const text = ev.target?.result as string;
      const lines = text.split('\n').filter(Boolean);
      const rows = lines.map((l) => l.split(',').map((c) => c.trim()));
      setPreview(rows.slice(0, 6));
    };
    reader.readAsText(f);
  };

  const handleImport = async () => {
    if (!file) return;
    setImporting(true);
    setResult(null);

    try {
      const base = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
      const token = localStorage.getItem('access_token');
      const form = new FormData();
      form.append('file', file);

      const res = await fetch(`${base}/import/${type}`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}` },
        body: form,
      });

      const body = await res.json();
      if (body.success) {
        setResult(body.data);
        toast(body.message, 'success');
      } else {
        toast(body.message || 'Import failed', 'error');
      }
    } catch {
      toast('Import failed', 'error');
    } finally {
      setImporting(false);
    }
  };

  const downloadTemplate = () => {
    const headers = type === 'students'
      ? 'name,email,password,phone,address,gender,nisn'
      : 'name,email,password,phone,address,gender';
    const blob = new Blob([headers + '\n'], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${type}-template.csv`;
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <ProtectedRoute roles={['admin']}>
      <MainLayout>
        <div className="max-w-2xl mx-auto space-y-6">
          <h1 className="text-2xl font-bold text-foreground">Bulk Import</h1>

          {/* Type selector */}
          <div className="rounded-xl border bg-card text-card-foreground shadow-sm border p-6">
            <label className="block text-sm font-medium text-foreground/80 mb-2">Import Type</label>
            <div className="flex gap-4">
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="type" value="students" checked={type === 'students'} onChange={() => { setType('students'); setFile(null); setPreview([]); setResult(null); }} className="text-blue-600" />
                <span className="text-sm text-foreground">Students</span>
              </label>
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="type" value="teachers" checked={type === 'teachers'} onChange={() => { setType('teachers'); setFile(null); setPreview([]); setResult(null); }} className="text-blue-600" />
                <span className="text-sm text-foreground">Teachers</span>
              </label>
            </div>
          </div>

          {/* Template download */}
          <div className="rounded-xl border bg-card text-card-foreground shadow-sm border p-6">
            <p className="text-sm text-foreground/70 mb-3">Download a template CSV to see the required columns:</p>
            <button onClick={downloadTemplate} className="px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-indigo-100">
              Download {type === 'students' ? 'Students' : 'Teachers'} Template
            </button>
          </div>

          {/* File upload */}
          <div className="rounded-xl border bg-card text-card-foreground shadow-sm border p-6">
            <label className="block text-sm font-medium text-foreground/80 mb-2">Upload CSV File</label>
            <input
              ref={fileRef}
              type="file"
              accept=".csv,.txt"
              onChange={handleFile}
              className="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-indigo-100"
            />
            <p className="mt-1 text-xs text-muted-foreground/60">Max 2MB, .csv or .txt files only</p>
          </div>

          {/* Preview */}
          {preview.length > 0 && (
            <div className="rounded-xl border bg-card text-card-foreground shadow-sm border p-6">
              <h3 className="text-sm font-medium text-foreground/80 mb-2">Preview (first {Math.min(preview.length, 5)} rows)</h3>
              <div className="overflow-x-auto text-xs">
                <table className="min-w-full divide-y divide-border">
                  <thead className="bg-muted/50">
                    <tr>
                      {preview[0]?.map((h, i) => <th key={i} className="px-3 py-2 text-left font-medium text-muted-foreground">{h}</th>)}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border">
                    {preview.slice(1).map((row, ri) => (
                      <tr key={ri}>
                        {row.map((cell, ci) => <td key={ci} className="px-3 py-2 text-foreground/80">{cell}</td>)}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              {preview.length > 5 && <p className="text-xs text-muted-foreground/60 mt-2">...and {preview.length - 5} more rows</p>}
            </div>
          )}

          {/* Import button */}
          {file && (
            <button
              onClick={handleImport}
              disabled={importing}
              className="w-full px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-md hover:from-blue-700 hover:to-blue-600 disabled:opacity-50"
            >
              {importing ? 'Importing...' : `Import ${type === 'students' ? 'Students' : 'Teachers'}`}
            </button>
          )}

          {/* Result */}
          {result && (
            <div className={`rounded-lg border p-4 ${result.errors.length === 0 ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'}`}>
              <p className="text-sm font-medium text-foreground">Imported {result.created} record(s)</p>
              {result.errors.length > 0 && (
                <ul className="mt-2 text-xs text-red-600 space-y-1">
                  {result.errors.map((e, i) => <li key={i}>{e}</li>)}
                </ul>
              )}
            </div>
          )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
