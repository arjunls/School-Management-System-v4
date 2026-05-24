"use client";
import React from 'react';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  icon?: React.ReactNode;
}

export function Input({ label, error, icon, className = '', id, ...props }: InputProps) {
  const inputId = id || props.name;
  return (
    <div className="space-y-1.5">
      {label && (
        <label htmlFor={inputId} className="block text-sm font-medium text-foreground/70">
          {label}
        </label>
      )}
      <div className="relative group">
        {icon && (
          <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted-foreground/40 group-focus-within:text-primary/60 transition-colors">
            {icon}
          </div>
        )}
        <input
          id={inputId}
          className={`
            flex h-10 w-full rounded-xl border bg-background px-3.5 py-1 text-sm shadow-sm transition-all
            file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground
            placeholder:text-muted-foreground/40
            focus-visible:border-primary/40 focus-visible:ring-[3px] focus-visible:ring-primary/10 focus-visible:shadow-md focus-visible:shadow-primary/5
            disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-muted
            ${icon ? 'pl-10' : ''}
            ${error ? 'border-destructive/60 focus-visible:border-destructive focus-visible:ring-destructive/10' : 'border-border/60'}
            ${className}
          `}
          {...props}
        />
      </div>
      {error && <p className="text-xs text-destructive/80 mt-0.5">{error}</p>}
    </div>
  );
}

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  label?: string;
  error?: string;
  options: { value: string; label: string }[];
  placeholder?: string;
}

export function Select({ label, error, options, placeholder, className = '', id, ...props }: SelectProps) {
  const selectId = id || props.name;
  return (
    <div className="space-y-1.5">
      {label && (
        <label htmlFor={selectId} className="block text-sm font-medium text-foreground/70">
          {label}
        </label>
      )}
      <select
        id={selectId}
        className={`
          flex h-10 w-full rounded-xl border bg-card px-3.5 py-1 text-sm shadow-sm transition-all
          placeholder:text-muted-foreground/40
          focus-visible:border-primary/40 focus-visible:ring-[3px] focus-visible:ring-primary/10
          disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-muted
          ${error ? 'border-destructive/60 focus-visible:border-destructive focus-visible:ring-destructive/10' : 'border-border/60'}
          ${className}
        `}
        {...props}
      >
        {placeholder && <option value="" className="text-muted-foreground">{placeholder}</option>}
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
      {error && <p className="text-xs text-destructive/80 mt-0.5">{error}</p>}
    </div>
  );
}

interface FormFieldProps {
  label?: string;
  error?: string;
  children: React.ReactNode;
}

export function FormField({ label, error, children }: FormFieldProps) {
  return (
    <div className="space-y-1.5">
      {label && <label className="block text-sm font-medium text-foreground/70">{label}</label>}
      {children}
      {error && <p className="text-xs text-destructive/80">{error}</p>}
    </div>
  );
}
