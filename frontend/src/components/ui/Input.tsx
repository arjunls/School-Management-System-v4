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
        <label htmlFor={inputId} className="block text-sm font-medium text-foreground/80">
          {label}
        </label>
      )}
      <div className="relative">
        {icon && (
          <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted-foreground">
            {icon}
          </div>
        )}
        <input
          id={inputId}
          className={`
            flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-sm transition-all
            file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground
            placeholder:text-muted-foreground/60
            focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20
            disabled:cursor-not-allowed disabled:opacity-50
            ${icon ? 'pl-9' : ''}
            ${error ? 'border-destructive focus-visible:border-destructive focus-visible:ring-destructive/20' : 'border-input'}
            ${className}
          `}
          {...props}
        />
      </div>
      {error && <p className="text-xs text-destructive mt-0.5">{error}</p>}
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
        <label htmlFor={selectId} className="block text-sm font-medium text-foreground/80">
          {label}
        </label>
      )}
      <select
        id={selectId}
        className={`
          flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-sm shadow-sm transition-all
          placeholder:text-muted-foreground/60
          focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20
          disabled:cursor-not-allowed disabled:opacity-50
          ${error ? 'border-destructive focus-visible:border-destructive focus-visible:ring-destructive/20' : 'border-input'}
          ${className}
        `}
        {...props}
      >
        {placeholder && <option value="">{placeholder}</option>}
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
      {error && <p className="text-xs text-destructive mt-0.5">{error}</p>}
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
      {label && <label className="block text-sm font-medium text-foreground/80">{label}</label>}
      {children}
      {error && <p className="text-xs text-destructive">{error}</p>}
    </div>
  );
}
