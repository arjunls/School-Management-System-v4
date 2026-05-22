import { render, screen, fireEvent } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import { LanguageSwitcher } from '@/components/ui/LanguageSwitcher'
import { I18nProvider } from '@/i18n/I18nProvider'

describe('LanguageSwitcher', () => {
  it('renders with default English selected', () => {
    render(
      <I18nProvider>
        <LanguageSwitcher />
      </I18nProvider>
    )
    const select = screen.getByRole('combobox') as HTMLSelectElement
    expect(select.value).toBe('en')
  })

  it('switches to Indonesian', () => {
    render(
      <I18nProvider>
        <LanguageSwitcher />
      </I18nProvider>
    )
    const select = screen.getByRole('combobox') as HTMLSelectElement
    fireEvent.change(select, { target: { value: 'id' } })
    expect(select.value).toBe('id')
  })
})
