"use client";
import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { authAPI } from '@/lib/api';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  phone?: string;
  address?: string;
  email_verified_at?: string | null;
  spatie_roles?: string[];
  permissions?: string[];
  status?: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  isAuthenticated: boolean;
  isAdmin: boolean;
  refreshToken: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

function setTokenCookie(token: string) {
  if (typeof document === 'undefined') return;
  // Set a non-httpOnly cookie so middleware can read it
  document.cookie = `access_token=${token}; path=/; max-age=${7 * 24 * 60 * 60}; samesite=strict`;
}

function removeTokenCookie() {
  if (typeof document === 'undefined') return;
  document.cookie = 'access_token=; path=/; max-age=0';
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  const isAdmin = user?.role === 'admin';

  const extractData = (responseData: unknown): { user?: User; token?: string } => {
    const raw = responseData as Record<string, unknown>;
    const payload = (raw?.data ?? raw) as Record<string, unknown>;
    if (payload && typeof payload === 'object' && 'user' in payload) {
      return { user: (payload as Record<string, unknown>).user as User, token: (payload as Record<string, unknown>).token as string };
    }
    return { user: payload as unknown as User };
  };

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await authAPI.getProfile();
        const { user: profileUser } = extractData(response.data);
        if (profileUser) {
          setUser(profileUser);
        }
      } catch (error) {
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    // Only check auth if token exists
    if (typeof window !== 'undefined' && localStorage.getItem('access_token')) {
      checkAuth();
    } else {
      setLoading(false);
    }
  }, []);

  const login = async (email: string, password: string) => {
    setLoading(true);
    try {
      const response = await authAPI.login(email, password);
      const { user: loggedInUser, token } = extractData(response.data);

      if (token && typeof window !== 'undefined') {
        localStorage.setItem('access_token', token);
        setTokenCookie(token);
      }

      if (loggedInUser) {
        setUser(loggedInUser);
      }

      router.push('/dashboard');
    } catch (error) {
      throw new Error('Invalid credentials');
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    setLoading(true);
    try {
      await authAPI.logout();
      if (typeof window !== 'undefined') {
        localStorage.removeItem('access_token');
        removeTokenCookie();
      }
      setUser(null);
      router.push('/login');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setLoading(false);
    }
  };

  const refreshToken = useCallback(async () => {
    try {
      const response = await authAPI.refresh();
      const { user: refreshedUser, token } = extractData(response.data);

      if (token && typeof window !== 'undefined') {
        localStorage.setItem('access_token', token);
        setTokenCookie(token);
      }

      if (refreshedUser) {
        setUser(refreshedUser);
      }
    } catch (error) {
      console.error('Token refresh failed:', error);
      // Force logout on refresh failure
      if (typeof window !== 'undefined') {
        localStorage.removeItem('access_token');
        removeTokenCookie();
      }
      setUser(null);
    }
  }, []);

  const value = {
    user,
    loading,
    login,
    logout,
    isAuthenticated: !!user,
    isAdmin,
    refreshToken,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
