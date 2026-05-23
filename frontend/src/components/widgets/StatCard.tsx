"use client";
import React from 'react';
import { motion } from 'framer-motion';

interface StatCardProps {
  title: string;
  value: string | number;
  icon: React.ReactNode;
  trend?: { value: string; isPositive: boolean };
  accent?: string;
  className?: string;
}

export function StatCard({ title, value, icon, trend, accent = 'var(--primary)', className = '' }: StatCardProps) {
  return (
    <motion.div
      variants={{ initial: { opacity: 0, y: 16 }, animate: { opacity: 1, y: 0 } }}
      whileHover={{ y: -2, boxShadow: 'var(--shadow-card-hover)' }}
      className={`rounded-xl border bg-card text-card-foreground shadow-card p-5 transition-all duration-200 ${className}`}
      style={{ ['--card-accent' as string]: accent }}
    >
      <div className="flex items-center justify-between mb-3">
        <span className="text-sm text-muted-foreground font-medium">{title}</span>
        <div className="size-9 rounded-lg flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
          style={{ background: `color-mix(in oklch, ${accent}, transparent 88%)`, color: accent }}
        >
          {icon}
        </div>
      </div>
      <div className="flex items-end justify-between">
        <span className="text-2xl font-bold tracking-tight">{value}</span>
        {trend && (
          <span className={`text-xs font-medium inline-flex items-center gap-0.5 ${trend.isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'}`}>
            <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
              {trend.isPositive
                ? <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                : <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.95 11.95 0 015.814 5.519l2.74 1.22m0 0l-5.94 2.28m5.94-2.28l-2.28-5.941" />
              }
            </svg>
            {trend.value}
          </span>
        )}
      </div>
    </motion.div>
  );
}
