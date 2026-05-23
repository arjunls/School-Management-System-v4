"use client";
import React, { useState } from 'react';
import { authAPI } from '@/lib/api';
import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { Suspense } from 'react';
import { motion } from 'framer-motion';

const fadeUp = {
  initial: { opacity: 0, y: 20 },
  animate: { opacity: 1, y: 0 },
};

const stagger = {
  animate: { transition: { staggerChildren: 0.07 } },
};

function ResetForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const token = searchParams.get('token') || '';
  const emailParam = searchParams.get('email') || '';

  const [form, setForm] = useState({ email: emailParam, token, password: '', password_confirmation: '' });
  const [loading, setLoading] = useState(false);
  const [done, setDone] = useState(false);
  const [error, setError] = useState('');
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setErrors({});
    try {
      await authAPI.resetPassword(form);
      setDone(true);
    } catch (err: any) {
      if (err?.response?.data?.errors) setErrors(err.response.data.errors);
      else setError(err?.response?.data?.message || 'Gagal mereset password');
    } finally {
      setLoading(false);
    }
  };

  if (done) {
    return (
      <div className="flex min-h-svh items-center justify-center bg-gradient-to-br from-blue-600/5 via-background to-orange-500/5 p-4">
        <div className="w-full max-w-md rounded-2xl border border-border/60 bg-card/90 backdrop-blur-xl text-card-foreground shadow-2xl shadow-blue-500/10 p-8 text-center">
          <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/25">
            <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
              <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
            </svg>
          </div>
          <h1 className="text-2xl font-bold mb-2">Password Berhasil Diubah</h1>
          <p className="text-muted-foreground mb-6">Password Anda telah berhasil direset.</p>
          <Link href="/login" className="inline-flex items-center justify-center rounded-xl text-sm font-semibold transition-all h-10 px-6 bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30">
            Masuk Sekarang
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="relative flex min-h-svh items-center justify-center overflow-hidden bg-gradient-to-br from-blue-600/5 via-background to-orange-500/5 p-4">
      <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,oklch(0.55_0.19_240/0.08),transparent)]" />
      <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_60%_40%_at_80%_80%,oklch(0.65_0.15_70/0.06),transparent)]" />

      <motion.div
        initial={{ opacity: 0, y: 30, scale: 0.97 }}
        animate={{ opacity: 1, y: 0, scale: 1 }}
        transition={{ duration: 0.5, ease: 'easeOut' }}
        className="relative w-full max-w-md"
      >
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8 flex items-center justify-center gap-3"
        >
          <Link href="/" className="flex items-center justify-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-500 shadow-lg shadow-blue-500/25">
              <svg className="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
              </svg>
            </div>
            <div className="flex flex-col items-start">
              <span className="text-xl font-bold tracking-tight text-foreground">SMK Nusantara</span>
              <span className="text-[11px] font-medium text-muted-foreground -mt-0.5">School Management System</span>
            </div>
          </Link>
        </motion.div>

        <motion.div
          variants={stagger}
          initial="initial"
          animate="animate"
          className="rounded-2xl border border-border/60 bg-card/90 backdrop-blur-xl text-card-foreground shadow-2xl shadow-blue-500/10"
        >
          <motion.div variants={fadeUp} className="flex flex-col space-y-1.5 p-7 pb-0 text-center">
            <div className="flex items-center justify-center gap-2">
              <div className="h-6 w-1 rounded-full bg-gradient-to-b from-blue-500 to-blue-600" />
              <div className="font-semibold tracking-tight text-2xl">Reset Password</div>
              <div className="h-6 w-1 rounded-full bg-gradient-to-b from-orange-400 to-orange-500" />
            </div>
            <div className="text-sm text-muted-foreground">Masukkan password baru Anda</div>
          </motion.div>

          <div className="p-7 pt-5">
            <form onSubmit={handleSubmit} className="space-y-4">
              <motion.div variants={fadeUp} className="space-y-1.5">
                <label htmlFor="email" className="flex items-center gap-1.5 text-sm leading-none font-medium select-none text-foreground/80">
                  <svg className="size-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                  </svg>
                  Email
                </label>
                <div className="group relative">
                  <div className="pointer-events-none absolute -inset-0.5 rounded-xl opacity-0 transition-all duration-300 group-focus-within:opacity-100 bg-gradient-to-r from-blue-500/20 to-orange-500/10 blur" />
                  <input
                    id="email"
                    type="email"
                    placeholder="nama@sekolah.com"
                    required
                    value={form.email}
                    onChange={e => setForm(p => ({ ...p, email: e.target.value }))}
                    disabled={loading}
                    className="relative file:text-foreground placeholder:text-muted-foreground selection:bg-blue-500/20 selection:text-blue-700 dark:bg-white/5 border-input h-10 w-full min-w-0 rounded-xl border bg-white/50 px-3 py-1 text-sm shadow-sm transition-all outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 focus-visible:border-blue-400 focus-visible:ring-4 focus-visible:ring-blue-500/10"
                  />
                </div>
              </motion.div>

              <motion.div variants={fadeUp} className="space-y-1.5">
                <label htmlFor="password" className="flex items-center gap-1.5 text-sm leading-none font-medium select-none text-foreground/80">
                  <svg className="size-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
                  Password Baru
                </label>
                <div className="group relative">
                  <div className="pointer-events-none absolute -inset-0.5 rounded-xl opacity-0 transition-all duration-300 group-focus-within:opacity-100 bg-gradient-to-r from-blue-500/20 to-orange-500/10 blur" />
                  <input
                    id="password"
                    type="password"
                    placeholder="Masukkan password baru"
                    required
                    value={form.password}
                    onChange={e => setForm(p => ({ ...p, password: e.target.value }))}
                    disabled={loading}
                    className="relative file:text-foreground placeholder:text-muted-foreground selection:bg-blue-500/20 selection:text-blue-700 dark:bg-white/5 border-input h-10 w-full min-w-0 rounded-xl border bg-white/50 px-3 py-1 text-sm shadow-sm transition-all outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 focus-visible:border-blue-400 focus-visible:ring-4 focus-visible:ring-blue-500/10"
                  />
                </div>
                {errors.password?.map((e, i) => (
                  <p key={i} className="text-xs text-red-500">{e}</p>
                ))}
              </motion.div>

              <motion.div variants={fadeUp} className="space-y-1.5">
                <label htmlFor="password_confirmation" className="flex items-center gap-1.5 text-sm leading-none font-medium select-none text-foreground/80">
                  <svg className="size-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                  </svg>
                  Konfirmasi Password
                </label>
                <div className="group relative">
                  <div className="pointer-events-none absolute -inset-0.5 rounded-xl opacity-0 transition-all duration-300 group-focus-within:opacity-100 bg-gradient-to-r from-blue-500/20 to-orange-500/10 blur" />
                  <input
                    id="password_confirmation"
                    type="password"
                    placeholder="Konfirmasi password baru"
                    required
                    value={form.password_confirmation}
                    onChange={e => setForm(p => ({ ...p, password_confirmation: e.target.value }))}
                    disabled={loading}
                    className="relative file:text-foreground placeholder:text-muted-foreground selection:bg-blue-500/20 selection:text-blue-700 dark:bg-white/5 border-input h-10 w-full min-w-0 rounded-xl border bg-white/50 px-3 py-1 text-sm shadow-sm transition-all outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 focus-visible:border-blue-400 focus-visible:ring-4 focus-visible:ring-blue-500/10"
                  />
                </div>
              </motion.div>

              {error && (
                <motion.div
                  initial={{ opacity: 0, y: -8 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 p-3.5 text-sm text-red-700 dark:text-red-300"
                >
                  <div className="flex items-center gap-2">
                    <svg className="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    {error}
                  </div>
                </motion.div>
              )}

              <motion.button
                variants={fadeUp}
                type="submit"
                disabled={loading}
                whileHover={!loading ? { scale: 1.01 } : {}}
                whileTap={!loading ? { scale: 0.99 } : {}}
                className="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-sm font-semibold transition-all disabled:pointer-events-none disabled:opacity-50 outline-none h-10 px-4 py-2 w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 active:shadow-sm"
              >
                {loading ? (
                  <>
                    <svg className="size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Mereset...
                  </>
                ) : (
                  <>
                    Reset Password
                    <svg className="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                  </>
                )}
              </motion.button>
            </form>
          </div>
        </motion.div>

        <motion.p
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ delay: 0.8 }}
          className="mt-6 text-center text-[11px] text-muted-foreground/60"
        >
          &copy; {new Date().getFullYear()} SMK Nusantara — All rights reserved
        </motion.p>
      </motion.div>
    </div>
  );
}

export default function ResetPasswordPage() {
  return (
    <Suspense fallback={
      <div className="flex min-h-svh items-center justify-center bg-gradient-to-br from-blue-600/5 via-background to-orange-500/5 text-muted-foreground">
        <div className="text-center">
          <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/25">
            <svg className="h-6 w-6 text-white animate-spin" viewBox="0 0 24 24" fill="none">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
          </div>
          <p className="text-sm">Memuat...</p>
        </div>
      </div>
    }>
      <ResetForm />
    </Suspense>
  );
}
