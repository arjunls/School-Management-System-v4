import React from 'react';

interface BadgeProps {
  variant?: 'default' | 'success' | 'warning' | 'danger' | 'info';
  size?: 'sm' | 'md';
  children: React.ReactNode;
  className?: string;
  dot?: boolean;
  pill?: boolean;
}

const variantMap: Record<string, string> = {
  default: 'bg-primary/8 text-primary border-primary/15',
  success: 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-900/30',
  warning: 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-900/30',
  danger: 'bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 border-red-200/50 dark:border-red-900/30',
  info: 'bg-sky-50 dark:bg-sky-950/20 text-sky-600 dark:text-sky-400 border-sky-200/50 dark:border-sky-900/30',
};

const sizeMap: Record<string, string> = {
  sm: 'text-[10px] px-2 py-0.5',
  md: 'text-xs px-2.5 py-0.5',
};

const dotColors: Record<string, string> = {
  default: 'bg-primary',
  success: 'bg-emerald-500',
  warning: 'bg-amber-500',
  danger: 'bg-red-500',
  info: 'bg-sky-500',
};

export function Badge({ variant = 'default', size = 'sm', children, className = '', dot = true, pill = true }: BadgeProps) {
  return (
    <span className={`inline-flex items-center gap-1.5 border font-medium leading-none shadow-sm ${pill ? 'rounded-full' : 'rounded-lg'} ${variantMap[variant]} ${sizeMap[size]} ${className}`}>
      {dot && <span className={`size-1.5 rounded-full ${dotColors[variant]}`} />}
      {children}
    </span>
  );
}
