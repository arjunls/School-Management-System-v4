'use client';
import { useTranslation } from '@/i18n/I18nProvider';

export function LanguageSwitcher() {
  const { locale, setLocale } = useTranslation();

  return (
    <select
      value={locale}
      onChange={(e) => setLocale(e.target.value as 'en' | 'id')}
      className="text-xs border border-gray-300 rounded px-2 py-1 bg-white"
    >
      <option value="en">EN</option>
      <option value="id">ID</option>
    </select>
  );
}
