import { render, screen, fireEvent } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import { I18nProvider, useTranslation } from '@/i18n/I18nProvider'

function TestComponent() {
  const { t, locale, setLocale } = useTranslation()
  return (
    <div>
      <span data-testid="locale">{locale}</span>
      <span data-testid="dashboard">{t('dashboard.title')}</span>
      <span data-testid="common">{t('common.search')}</span>
      <button data-testid="switch" onClick={() => setLocale('id')}>Switch</button>
    </div>
  )
}

describe('I18nProvider', () => {
  it('provides default English locale', () => {
    render(
      <I18nProvider>
        <TestComponent />
      </I18nProvider>
    )
    expect(screen.getByTestId('locale').textContent).toBe('en')
  })

  it('translates known keys', () => {
    render(
      <I18nProvider>
        <TestComponent />
      </I18nProvider>
    )
    expect(screen.getByTestId('dashboard').textContent).toBeTruthy()
  })

  it('switches locale', () => {
    render(
      <I18nProvider>
        <TestComponent />
      </I18nProvider>
    )
    fireEvent.click(screen.getByTestId('switch'))
    expect(screen.getByTestId('locale').textContent).toBe('id')
  })
})
