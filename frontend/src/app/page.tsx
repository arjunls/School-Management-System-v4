"use client";
import { useAuth } from '@/contexts/AuthContext';
import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

export default function HomePage() {
  const { user, loading } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!loading) {
      if (user) {
        router.push('/dashboard');
      } else {
        router.push('/login');
      }
    }
  }, [user, loading, router]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="inline-block animate-pulse h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-bold">
            SM
          </div>
          <h2 className="mt-4 text-lg font-bold text-gray-900">
            School Management System
          </h2>
          <p className="mt-2 text-sm text-gray-500">
            Loading...
          </p>
        </div>
      </div>
    );
  }

  return null; // Redirect handled in useEffect
}