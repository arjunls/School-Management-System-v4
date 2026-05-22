'use client';
import React, { createContext, useContext, useState, useCallback } from 'react';
import en from './locales/en.json';
import id from './locales/id.json';

const resources: Record<string, any> = { en, id };

type Locale = 'en' | 'id';

interface I18nContextType {
  locale: Locale;
  setLocale: (l: Locale) => void;
  t: (key: string) => string;
  translate: (key: string, params?: Record<string, string | number>) => string;
}

const I18nContext = createContext<I18nContextType>({
  locale: 'en',
  setLocale: () => {},
  t: () => '',
  translate: () => '',
});

function resolve(obj: any, path: string): string {
  const keys = path.split('.');
  let current = obj;
  for (const k of keys) {
    if (current?.[k] !== undefined) current = current[k];
    else return path;
  }
  return typeof current === 'string' ? current : path;
}

export function I18nProvider({ children }: { children: React.ReactNode }) {
  const [locale, setLocale] = useState<Locale>('en');

  const t = useCallback((key: string) => resolve(resources[locale], key), [locale]);
  const translate = useCallback(
    (key: string, params?: Record<string, string | number>) => {
      let str = resolve(resources[locale], key);
      if (params) {
        for (const [k, v] of Object.entries(params)) {
          str = str.replace(`{${k}}`, String(v));
        }
      }
      return str;
    },
    [locale],
  );

  return (
    <I18nContext.Provider value={{ locale, setLocale, t, translate }}>
      {children}
    </I18nContext.Provider>
  );
}

export function useTranslation() {
  return useContext(I18nContext);
}
