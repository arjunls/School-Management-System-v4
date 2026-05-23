"use client";
import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

export interface Column<T> {
  key: string;
  label: string;
  sortable?: boolean;
  render?: (row: T, index: number) => React.ReactNode;
  className?: string;
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[];
  keyExtractor: (row: T) => string | number;
  loading?: boolean;
  emptyMessage?: string;
  pageSize?: number;
}

function LoadingRows({ columns }: { columns: number }) {
  return (
    <>
      {[...Array(5)].map((_, i) => (
        <tr key={i} className="animate-pulse">
          {[...Array(columns)].map((_, j) => (
            <td key={j} className="px-4 py-3"><div className="h-3.5 bg-muted rounded w-3/4" /></td>
          ))}
        </tr>
      ))}
    </>
  );
}

export function DataTable<T extends object>({ columns, data, keyExtractor, loading, emptyMessage = 'Tidak ada data.', pageSize = 10 }: DataTableProps<T>) {
  const [sortKey, setSortKey] = useState<string | null>(null);
  const [sortDir, setSortDir] = useState<'asc' | 'desc'>('asc');
  const [page, setPage] = useState(0);

  const sorted = React.useMemo(() => {
    if (!sortKey) return data;
    return [...data].sort((a, b) => {
      const aVal = (a as Record<string, unknown>)[sortKey];
      const bVal = (b as Record<string, unknown>)[sortKey];
      if (aVal == null) return 1;
      if (bVal == null) return -1;
      const cmp = String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
      return sortDir === 'asc' ? cmp : -cmp;
    });
  }, [data, sortKey, sortDir]);

  const totalPages = Math.max(1, Math.ceil(sorted.length / pageSize));
  const paged = sorted.slice(page * pageSize, (page + 1) * pageSize);

  const handleSort = (key: string) => {
    if (sortKey === key) {
      setSortDir(d => d === 'asc' ? 'desc' : 'asc');
    } else {
      setSortKey(key);
      setSortDir('asc');
    }
  };

  if (loading) {
    return (
      <div className="rounded-xl border bg-card overflow-hidden">
        <table className="w-full"><thead><tr>{columns.map(c => <th key={c.key} className="h-9 px-4 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground bg-muted/30 border-b border-border">{c.label}</th>)}</tr></thead>
          <tbody className="divide-y divide-border"><LoadingRows columns={columns.length} /></tbody></table>
      </div>
    );
  }

  return (
    <div className="rounded-xl border bg-card overflow-hidden transition-all duration-200 hover:shadow-card-hover">
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-border bg-muted/20">
              {columns.map(col => (
                <th
                  key={col.key}
                  onClick={col.sortable ? () => handleSort(col.key) : undefined}
                  className={`h-9 px-4 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground ${col.sortable ? 'cursor-pointer select-none hover:text-foreground transition-colors' : ''} ${col.className || ''}`}
                >
                  <span className="inline-flex items-center gap-1">
                    {col.label}
                    {col.sortable && sortKey === col.key && (
                      <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                        {sortDir === 'asc' ? (
                          <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        ) : (
                          <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        )}
                      </svg>
                    )}
                    {col.sortable && sortKey !== col.key && (
                      <svg className="size-3 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                      </svg>
                    )}
                  </span>
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-border">
            <AnimatePresence mode="popLayout">
              {paged.length === 0 ? (
                <tr>
                  <td colSpan={columns.length} className="px-4 py-12 text-center text-sm text-muted-foreground">
                    {emptyMessage}
                  </td>
                </tr>
              ) : (
                paged.map((row, i) => (
                  <motion.tr
                    key={keyExtractor(row)}
                    layout
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.15 }}
                    className="hover:bg-primary/5 transition-colors group"
                  >
                    {columns.map(col => (
                      <td key={col.key} className={`px-4 py-2.5 text-sm ${col.className || ''}`}>
                        <span className="block truncate max-w-[200px] lg:max-w-none">
                          {col.render ? col.render(row, i) : String((row as Record<string, unknown>)[col.key] ?? '—')}
                        </span>
                      </td>
                    ))}
                  </motion.tr>
                ))
              )}
            </AnimatePresence>
          </tbody>
        </table>
      </div>

      {totalPages > 1 && (
        <div className="flex items-center justify-between border-t border-border px-4 py-2.5">
          <span className="text-xs text-muted-foreground">
            {sorted.length} data · Halaman {page + 1}/{totalPages}
          </span>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(p => Math.max(0, p - 1))}
              disabled={page === 0}
              className="inline-flex items-center justify-center rounded-md border border-input bg-background px-2.5 py-1 text-xs font-medium transition-all hover:bg-accent hover:text-accent-foreground disabled:opacity-30 disabled:pointer-events-none"
            >
              <svg className="size-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
              Prev
            </button>
            {Array.from({ length: totalPages }, (_, i) => (
              <button key={i} onClick={() => setPage(i)}
                className={`size-7 rounded-md text-xs font-medium transition-all ${page === i ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-accent hover:text-accent-foreground text-muted-foreground'}`}
              >{i + 1}</button>
            ))}
            <button
              onClick={() => setPage(p => Math.min(totalPages - 1, p + 1))}
              disabled={page >= totalPages - 1}
              className="inline-flex items-center justify-center rounded-md border border-input bg-background px-2.5 py-1 text-xs font-medium transition-all hover:bg-accent hover:text-accent-foreground disabled:opacity-30 disabled:pointer-events-none"
            >
              Next
              <svg className="size-3.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
