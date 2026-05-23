'use client';
import { useState, useEffect } from 'react';

const mottos = [
  'Mencetak Lulusan Berkompeten dan Berkarakter',
  'Siap Kerja, Siap Wirausaha, Siap Lanjut Studi',
  'SMK Bisa — SMK Hebat',
];

export function TypeText({ className }: { className?: string }) {
  const [idx, setIdx] = useState(0);
  const [char, setChar] = useState(0);
  const [dir, setDir] = useState(1);

  useEffect(() => {
    const t = setInterval(() => {
      setChar(prev => {
        const next = prev + dir;
        if (next > mottos[idx].length || next < 0) {
          if (dir === 1 && next > mottos[idx].length) {
            setTimeout(() => setDir(-1), 1500);
            return prev;
          }
          if (dir === -1 && next < 0) {
            setIdx((i) => (i + 1) % mottos.length);
            setDir(1);
            return 0;
          }
          return prev;
        }
        return next;
      });
    }, 45);
    return () => clearInterval(t);
  }, [idx, dir]);

  return (
    <p className={className}>
      &ldquo;{mottos[idx].slice(0, char)}&rdquo;
      <span className="animate-pulse text-white/40">|</span>
    </p>
  );
}
