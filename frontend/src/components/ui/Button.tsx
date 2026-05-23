"use client";
import React from 'react';
import { motion } from 'framer-motion';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'ghost' | 'outline' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  icon?: React.ReactNode;
}

const variantStyles: Record<string, string> = {
  primary: 'bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-700 hover:to-blue-600 shadow-xs hover:shadow-md hover:shadow-blue-500/25',
  secondary: 'bg-card border border-input text-foreground hover:bg-accent hover:text-accent-foreground shadow-xs',
  ghost: 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
  outline: 'border border-input bg-background text-foreground hover:bg-accent hover:text-accent-foreground',
  danger: 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-xs',
};

const sizeStyles: Record<string, string> = {
  sm: 'h-8 px-3 text-xs gap-1.5',
  md: 'h-9 px-4 text-sm gap-2',
  lg: 'h-10 px-5 text-sm gap-2',
};

export function Button({ variant = 'primary', size = 'md', loading, icon, children, className = '', disabled, ...props }: ButtonProps) {
  return (
    <motion.button
      whileTap={{ scale: 0.97 }}
      className={`inline-flex items-center justify-center rounded-md font-medium transition-all duration-200 focus-visible:outline-2 focus-visible:outline-ring/50 disabled:pointer-events-none disabled:opacity-50 ${variantStyles[variant]} ${sizeStyles[size]} ${className}`}
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
      className={`inline-flex items-center justify-center rounded-md transition-all duration-200 ${variantStyles[variant]} ${sizeClass} ${className}`}
      {...(props as Record<string, unknown>)}
    >
      {children}
    </motion.button>
  );
}
