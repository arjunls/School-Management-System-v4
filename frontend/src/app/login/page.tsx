"use client";
import React, { useState, useEffect, useRef, useCallback } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { useDarkMode } from '@/contexts/DarkModeContext';
import { motion, AnimatePresence } from 'framer-motion';
import { FloatInput } from '@/components/login/FloatInput';
import { StrengthBar } from '@/components/login/StrengthBar';
import { TypeText } from '@/components/login/TypeText';
import { quickAccounts, particles, statItems, tools, themes } from '@/components/login/constants';

const formEnter = { initial: { opacity: 0, y: 16 }, animate: { opacity: 1, y: 0 } };
const formStagger = { animate: { transition: { staggerChildren: 0.08, delayChildren: 0.15 } } };

export default function LoginPage() {
  const { login } = useAuth();
  const { dark, toggle } = useDarkMode();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [pageReady, setPageReady] = useState(false);
  const [bannerDismissed, setBannerDismissed] = useState(false);
  const [mousePos, setMousePos] = useState({ x: 0.5, y: 0.5 });
  const rightRef = useRef<HTMLDivElement>(null);

  const hour = new Date().getHours();
  const greeting = hour < 11 ? 'Selamat Pagi' : hour < 15 ? 'Selamat Siang' : hour < 18 ? 'Selamat Sore' : 'Selamat Malam';

  const [themeIdx, setThemeIdx] = useState(0);
  const theme = themes[themeIdx];

  useEffect(() => {
    const stored = parseInt(localStorage.getItem('loginTheme') || '0');
    if (stored >= 0 && stored < themes.length) setThemeIdx(stored);
  }, []);

  useEffect(() => { localStorage.setItem('loginTheme', String(themeIdx)); }, [themeIdx]);

  useEffect(() => { setPageReady(true); }, []);

  const handleMouse = useCallback((e: React.MouseEvent) => {
    if (!rightRef.current) return;
    const rect = rightRef.current.getBoundingClientRect();
    setMousePos({ x: (e.clientX - rect.left) / rect.width, y: (e.clientY - rect.top) / rect.height });
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try { await login(email, password); }
    catch { setError('Email atau password salah'); }
    finally { setLoading(false); }
  };

  const fillAccount = (email: string) => {
    setEmail(email);
    setPassword('password');
  };

  if (!pageReady) {
    return (
      <div className="flex min-h-svh">
        <div className="flex w-full items-center justify-center p-6 lg:w-[460px]">
          <div className="w-full max-w-sm space-y-5">
            <div className="flex flex-col items-center gap-3 mb-8">
              <div className="h-9 w-9 rounded-xl bg-muted/50 animate-pulse" />
              <div className="h-4 w-28 rounded bg-muted/50 animate-pulse" />
            </div>
            <div className="h-6 w-32 mx-auto rounded bg-muted/50 animate-pulse mb-6" />
            <div className="h-14 rounded-xl bg-muted/50 animate-pulse" />
            <div className="h-14 rounded-xl bg-muted/50 animate-pulse" />
            <div className="h-9 rounded-xl bg-muted/50 animate-pulse" />
          </div>
        </div>
        <div suppressHydrationWarning className={`hidden flex-1 bg-gradient-to-br ${theme.right} lg:flex items-center justify-center`}>
          <div className="h-20 w-20 rounded-2xl bg-white/10 animate-pulse" />
        </div>
      </div>
    );
  }

  return (
    <div className="flex min-h-svh">
      <div className="relative flex w-full items-center justify-center p-6 lg:w-[460px]">
        <div className="pointer-events-none absolute inset-0 transition-all duration-700"
          style={{ background: `radial-gradient(ellipse 80% 50% at 50% -20%, ${theme.left}/0.08, transparent)` }}
        />
        <div className="pointer-events-none absolute inset-0 opacity-[0.03] dark:opacity-[0.05]"
          style={{ backgroundImage: 'radial-gradient(circle, currentColor 1px, transparent 1px)', backgroundSize: '24px 24px' }} />

        <motion.div
          initial={{ opacity: 0, x: -30 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.6, ease: 'easeOut' as const }}
          className="relative w-full max-w-sm"
        >
          <motion.div initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.1 }}
            className="mb-5 flex flex-col items-center gap-2"
          >
            <div className="flex items-center gap-2.5">
              <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-500 shadow-lg shadow-blue-500/25">
                <svg className="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                </svg>
              </div>
              <div>
                <span className="text-base font-bold tracking-tight text-foreground">SMK Nusantara</span>
                <span className="block text-[9px] font-medium text-muted-foreground leading-none">School Management System</span>
              </div>
            </div>
          </motion.div>

          <AnimatePresence>
            {!bannerDismissed && (
              <motion.div initial={{ opacity: 0, height: 0 }} animate={{ opacity: 1, height: 'auto' }} exit={{ opacity: 0, height: 0 }}
                className="mb-4 overflow-hidden"
              >
                <div className="flex items-start gap-2 rounded-xl bg-gradient-to-r from-blue-500/8 to-orange-500/8 border border-blue-500/15 p-3 text-xs">
                  <svg className="size-3.5 shrink-0 mt-0.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.497.497 0 0 1-.613-.096 15.039 15.039 0 0 1-2.147-3.403m6.53-6.374c-.253-.962-.584-1.892-.985-2.783-.247-.55-.06-1.21.463-1.511l.657-.38a.497.497 0 0 1 .613.096 15.04 15.04 0 0 1 2.147 3.403m-4.347 7.983a4.501 4.501 0 0 0 4.487-4.232m-4.487 4.232a8.973 8.973 0 0 1-3.214-5.301m6.21 1.723c.066.597.099 1.202.099 1.817 0 .615-.033 1.22-.098 1.817m-6.211-1.723a9.002 9.002 0 0 1-2.116-5.675" />
                  </svg>
                  <div className="flex-1 text-muted-foreground">
                    <span className="font-semibold text-foreground">Pengumuman:</span> Jadwal UAS Semester Genap telah tersedia. Cek di menu Jadwal.
                  </div>
                  <button onClick={() => setBannerDismissed(true)}
                    className="shrink-0 text-muted-foreground/40 hover:text-foreground transition-colors">
                    <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </motion.div>
            )}
          </AnimatePresence>

          <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.25 }}
            className="mb-5 text-center"
          >
            <h1 className="text-xl font-semibold tracking-tight">{greeting}</h1>
            <p className="text-sm text-muted-foreground mt-0.5">Masukkan kredensial akun Anda</p>
          </motion.div>

          <motion.div variants={formStagger} initial="initial" animate="animate">
            <form onSubmit={handleSubmit} className="space-y-3.5">
              <motion.div variants={formEnter}>
                <FloatInput id="email" type="email" value={email} onChange={e => setEmail(e.target.value)}
                  label="Email" icon="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" isBusy={loading} accent={theme.accent} />
              </motion.div>

              <motion.div variants={formEnter}>
                <FloatInput id="password" type={showPassword ? 'text' : 'password'} value={password} onChange={e => setPassword(e.target.value)}
                  label="Password" icon="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                  isBusy={loading} showToggle onToggle={() => setShowPassword(prev => !prev)} accent={theme.accent} />
                <StrengthBar password={password} />
              </motion.div>

              <motion.div variants={formEnter} className="flex items-center justify-between">
                <label className="flex cursor-pointer items-center gap-1.5 text-xs text-muted-foreground select-none hover:text-foreground transition-colors">
                  <input type="checkbox" className="size-3.5 rounded border border-input accent-blue-600 transition-all" defaultChecked />
                  Ingat saya
                </label>
                <a href="/forgot-password" className="text-xs font-medium text-blue-600 hover:text-blue-500 transition-colors">Lupa password?</a>
              </motion.div>

              <motion.button variants={formEnter} type="submit" disabled={loading}
                whileHover={!loading ? { scale: 1.01 } : {}} whileTap={!loading ? { scale: 0.98 } : {}}
                className="inline-flex items-center justify-center gap-2 w-full h-9 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-500/25 hover:from-blue-500 hover:to-blue-400 hover:shadow-xl hover:shadow-blue-500/30 active:shadow-sm transition-all disabled:opacity-50 disabled:pointer-events-none outline-none"
              >
                {loading ? (
                  <><svg className="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Memproses...</>
                ) : (
                  <><span>Masuk</span><svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg></>
                )}
              </motion.button>
            </form>

            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.65 }}
              className="mt-4 flex items-center justify-center gap-3"
            >
              <span className="text-[9px] text-muted-foreground/40">Akses cepat:</span>
              {quickAccounts.map((a, i) => (
                <motion.button key={i} type="button" onClick={() => fillAccount(a.email)}
                  whileHover={{ scale: 1.15 }} whileTap={{ scale: 0.9 }}
                  title={`Login sebagai ${a.name}`}
                  className={`flex size-7 items-center justify-center rounded-full bg-gradient-to-br ${a.color} text-[10px] font-bold text-white shadow-sm cursor-pointer`}
                >
                  {a.text}
                </motion.button>
              ))}
            </motion.div>

            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.75 }}
              className="mt-4 flex items-center justify-center gap-4"
            >
              {tools.map((t, i) => (
                <button key={i} type="button" title={t.label}
                  className="flex flex-col items-center gap-1 text-muted-foreground/40 hover:text-blue-500 transition-colors"
                >
                  <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d={t.icon} />
                  </svg>
                  <span className="text-[7px]">{t.label}</span>
                </button>
              ))}
            </motion.div>

            <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.85 }}
              className="mt-4 text-center text-[10px] text-muted-foreground/40">Digunakan oleh <span className="font-semibold text-muted-foreground/60">50+ SMK</span> di Indonesia</motion.p>

            <AnimatePresence>
              {error && (
                <motion.div initial={{ opacity: 0, y: -6, height: 0 }} animate={{ opacity: 1, y: 0, height: 'auto' }} exit={{ opacity: 0, y: -6, height: 0 }}
                  transition={{ duration: 0.2 }}
                  className="mt-3 overflow-hidden rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 p-3 text-sm text-red-700 dark:text-red-300"
                >
                  <div className="flex items-center gap-2">
                    <svg className="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    {error}
                  </div>
                </motion.div>
              )}
            </AnimatePresence>

            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.95 }}
              className="mt-5 flex items-center justify-center gap-4 text-[10px] text-muted-foreground/35"
            >
              <button onClick={toggle} className="flex items-center gap-1 hover:text-foreground transition-colors">
                  {dark ? (
                    <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                  ) : (
                    <svg className="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
                  )}
                {dark ? 'Mode Terang' : 'Mode Gelap'}
              </button>
              <span>&copy; {new Date().getFullYear()} v2.0</span>
            </motion.div>
          </motion.div>
        </motion.div>
      </div>

      <div ref={rightRef} onMouseMove={handleMouse}
        suppressHydrationWarning
        className={`hidden flex-1 items-center justify-center bg-gradient-to-br ${theme.right} relative overflow-hidden lg:flex`}
      >
        <div className="absolute inset-0 transition-all duration-75"
          style={{
            background: `radial-gradient(ellipse 60% 50% at ${30 + (mousePos.x - 0.5) * 8}% ${20 + (mousePos.y - 0.5) * 8}%, color-mix(in oklch, ${theme.left}, transparent 85%), transparent)`,
          }}
        />
        <div className="absolute inset-0 transition-all duration-75"
          style={{
            background: `radial-gradient(ellipse 50% 60% at ${70 + (mousePos.x - 0.5) * -6}% ${80 + (mousePos.y - 0.5) * -6}%, color-mix(in oklch, ${theme.left}, transparent 90%), transparent)`,
          }}
        />

        {particles.map((p, i) => (
          <motion.div key={i}
            className="absolute flex items-center justify-center rounded-2xl bg-white/[0.06] backdrop-blur-[1px] border border-white/10"
            style={{ width: p.size, height: p.size, left: `${p.x}%`, top: `${p.y}%` }}
            animate={{ y: [0, -20, 0], opacity: [0.4, 0.7, 0.4] }}
            transition={{ duration: p.dur, delay: p.del, repeat: Infinity, ease: 'easeInOut' }}
          >
            <svg className="text-white/60" style={{ width: p.size * 0.4, height: p.size * 0.4 }} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
              <path strokeLinecap="round" strokeLinejoin="round" d={p.icon} />
            </svg>
          </motion.div>
        ))}

        <motion.div initial={{ opacity: 0, scale: 0.92 }} animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.7, delay: 0.15 }}
          className="relative z-10 flex flex-col items-center text-center max-w-md px-8"
        >
          <motion.div animate={{ y: [0, -6, 0] }} transition={{ duration: 5, repeat: Infinity, ease: 'easeInOut' }}
            className="mb-7"
          >
            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 shadow-2xl">
              <svg className="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
              </svg>
            </div>
          </motion.div>

          <motion.h2 initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.35, duration: 0.5 }}
            className="text-2xl font-bold text-white mb-3">SMK Nusantara</motion.h2>

          <TypeText className="text-white/60 text-sm leading-relaxed mb-8 italic min-h-[3rem]" />

          <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.55, duration: 0.5 }}
            className="grid w-full grid-cols-3 gap-3"
          >
            {statItems.map((s, i) => (
              <motion.div key={i} whileHover={{ y: -2, scale: 1.02 }}
                className="rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 py-3 text-center"
              >
                <div className="text-lg font-bold text-white">{s.value}</div>
                <div className="text-[10px] text-white/50 mt-0.5">{s.label}</div>
              </motion.div>
            ))}
          </motion.div>
        </motion.div>
      </div>
    </div>
  );
}
