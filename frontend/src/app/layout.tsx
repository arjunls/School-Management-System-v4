import './globals.css';
import type { Metadata } from 'next';

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
    <html lang="en" suppressHydrationWarning>
      <head>
        <script dangerouslySetInnerHTML={{
          __html: `
            (function() {
              var stored = localStorage.getItem('theme');
              var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
              if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
              }
            })();
          `
        }} />
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
      </head>
      <body>
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