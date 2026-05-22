"use client";
import React, { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';

export default function LoginPage() {
  const { login, loading: authLoading } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      await login(email, password);
    } catch (err) {
      setError('Invalid email or password');
    } finally {
      setLoading(false);
    }
  };

  const isBusy = loading || authLoading;

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <div className="w-full max-w-md space-y-8 p-4">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900">
            School Management System
          </h2>
          <p className="text-sm text-gray-600">
            Sign in to your account
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              required
              className="block w-full rounded-md border-0 px-3.5 py-2 pb-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={isBusy}
            />
          </div>

          <div>
            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <input
              id="password"
              type="password"
              autoComplete="current-password"
              required
              className="block w-full rounded-md border-0 px-3.5 py-2 pb-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={isBusy}
            />
            <div className="mt-1 text-right">
              <a href="/forgot-password" className="text-xs text-indigo-600 hover:text-indigo-800">Forgot password?</a>
            </div>
          </div>

          <div className="flex w-full justify-center">
            <button
              type="submit"
              disabled={isBusy}
              className="flex w-full items-center justify-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-indigo-600 border border-transparent rounded-md active:bg-indigo-700 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:ring-offset-2 disabled:opacity-50"
            >
              {isBusy ? 'Signing in...' : 'Sign In'}
            </button>
          </div>
        </form>

        {error && (
          <div className="bg-red-50 border-l-4 border-red-500 p-4 text-sm text-red-700" role="alert">
            {error}
          </div>
        )}

        <div className="text-center text-sm text-gray-500">
          <p className="mt-2">
            Demo accounts: <code className="bg-gray-100 px-1 py-0.5 rounded">admin@school.com</code>,
            <code className="bg-gray-100 px-1 py-0.5 rounded">teacher@school.com</code>,
            <code className="bg-gray-100 px-1 py-0.5 rounded">student@school.com</code>
            <br />
            Password: <code className="bg-gray-100 px-1 py-0.5 rounded">password</code>
          </p>
        </div>
      </div>
    </div>
  );
}
