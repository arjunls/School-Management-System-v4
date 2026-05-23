'use client';
import { motion } from 'framer-motion';

export function StrengthBar({ password }: { password: string }) {
  const score = password.length === 0 ? 0
    : password.length < 6 ? 1
    : password.length < 10 ? 2
    : /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password) ? 4
    : /[A-Z]/.test(password) || /[0-9]/.test(password) ? 3
    : 2;

  const colors = ['', 'bg-red-500', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-500'];
  const labels = ['', 'Lemah', 'Cukup', 'Sedang', 'Kuat'];
  const w = score * 25;

  if (password.length === 0) return null;
  return (
    <div className="mt-1.5">
      <div className="h-1 rounded-full bg-muted/50 overflow-hidden">
        <motion.div initial={{ width: 0 }} animate={{ width: `${w}%` }}
          className={`h-full rounded-full ${colors[score]} transition-colors`} />
      </div>
      <p className={`text-[9px] mt-0.5 font-medium ${colors[score].replace('bg-', 'text-')}`}>{labels[score]}</p>
    </div>
  );
}
