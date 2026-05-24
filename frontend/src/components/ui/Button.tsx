"use client";
import React from 'react';
import { motion } from 'framer-motion';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'ghost' | 'outline' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  icon?: React.ReactNode;
  pill?: boolean;
}

const variantStyles: Record<string, string> = {
  primary: 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-500 hover:to-indigo-500 shadow-md hover:shadow-lg hover:shadow-blue-500/20 active:shadow-sm',
  secondary: 'bg-card border border-border/60 text-foreground hover:bg-muted hover:text-foreground shadow-sm',
  ghost: 'text-muted-foreground hover:bg-muted hover:text-foreground',
  outline: 'border border-border/60 bg-background text-foreground hover:bg-muted hover:text-foreground shadow-sm',
  danger: 'bg-gradient-to-r from-red-600 to-rose-600 text-white hover:from-red-500 hover:to-rose-500 shadow-md hover:shadow-lg hover:shadow-red-500/20 active:shadow-sm',
};

const sizeStyles: Record<string, string> = {
  sm: 'h-8 px-3 text-xs gap-1.5',
  md: 'h-9 px-4 text-sm gap-2',
  lg: 'h-10 px-5 text-sm gap-2',
};

export function Button({ variant = 'primary', size = 'md', loading, icon, children, className = '', disabled, pill = true, ...props }: ButtonProps) {
  const radius = pill ? 'rounded-full' : 'rounded-xl';
  return (
    <motion.button
      whileTap={{ scale: 0.97 }}
      className={`inline-flex items-center justify-center font-medium transition-all duration-150 focus-visible:outline-2 focus-visible:outline-ring/50 disabled:pointer-events-none disabled:opacity-50 ${variantStyles[variant]} ${sizeStyles[size]} ${radius} ${className}`}
      disabled={disabled || loading}
      {...(props as Record<string, unknown>)}
    >
      {loading ? (
        <svg className="animate-spin size-4" fill="none" viewBox="0 0 24 24">
          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
      ) : icon ? (
        <span className="shrink-0">{icon}</span>
      ) : null}
      {children}
    </motion.button>
  );
}

export function IconButton({ variant = 'ghost', size = 'md', children, className = '', ...props }: ButtonProps) {
  const sizeClass = size === 'sm' ? 'size-8' : size === 'lg' ? 'size-10' : 'size-9';
  return (
    <motion.button
      whileTap={{ scale: 0.9 }}
      className={`inline-flex items-center justify-center rounded-xl transition-all duration-150 ${variantStyles[variant]} ${sizeClass} ${className}`}
      {...(props as Record<string, unknown>)}
    >
      {children}
    </motion.button>
  );
}
