import './globals.css';
import type { Metadata } from 'next';
import { Inter } from 'next/font/google';

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  title: 'School Management System',
  description: 'Modern School Management Platform',
};

import { QueryProvider } from '@/app/query-provider';
import { AuthProvider } from '@/contexts/AuthContext';
import { ToastProvider } from '@/components/ui/Toast';
import { I18nProvider } from '@/i18n/I18nProvider';
import { DarkModeProvider } from '@/contexts/DarkModeContext';

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <AuthProvider>
          <DarkModeProvider>
            <I18nProvider>
              <QueryProvider>
                <ToastProvider>{children}</ToastProvider>
              </QueryProvider>
            </I18nProvider>
          </DarkModeProvider>
        </AuthProvider>
      </body>
    </html>
  );
}