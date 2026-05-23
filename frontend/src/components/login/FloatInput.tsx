'use client';
import React from 'react';

export function FloatInput({ id, type, value, onChange, label, icon, isBusy, showToggle, onToggle, accent }: {
  id: string; type: string; value: string; onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  label: string; icon: string; isBusy?: boolean; showToggle?: boolean; onToggle?: () => void; accent?: string;
}) {
  const hasVal = value.length > 0;
  return (
    <div className="group relative">
      <div className="pointer-events-none absolute -inset-0.5 rounded-xl opacity-0 transition-all duration-300 group-focus-within:opacity-100 blur"
        style={{ background: accent ? `linear-gradient(to right, ${accent}33, ${accent}1a)` : undefined }}
      />
      <div className="relative">
        <input
          id={id} type={type} required value={value} onChange={onChange} disabled={isBusy} placeholder=" "
          className="peer relative w-full rounded-xl border border-input bg-white/50 dark:bg-white/5 px-3 pt-5 pb-1 pr-9 text-sm shadow-sm outline-none transition-all file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-transparent disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 focus-visible:border-blue-400 focus-visible:ring-4 focus-visible:ring-blue-500/10"
        />
        <label htmlFor={id}
          className={`pointer-events-none absolute left-3 flex items-center gap-1.5 transition-all duration-200 select-none
            ${hasVal ? 'top-1.5 text-[10px] text-blue-500' : 'top-1/2 -translate-y-1/2 text-sm text-muted-foreground'}
            peer-focus:top-1.5 peer-focus:text-[10px] peer-focus:text-blue-500`}
        >
          <svg className={`size-3 shrink-0 transition-colors duration-200 ${hasVal ? 'text-blue-500' : 'text-muted-foreground'} peer-focus:text-blue-500`} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
            <path strokeLinecap="round" strokeLinejoin="round" d={icon} />
          </svg>
          {label}
        </label>
        {showToggle && (
          <button type="button" onClick={onToggle} tabIndex={-1}
            className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground/40 hover:text-foreground transition-colors"
          >
            {type === 'password' ? (
              <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            ) : (
              <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            )}
          </button>
        )}
      </div>
    </div>
  );
}
