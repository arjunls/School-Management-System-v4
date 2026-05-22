import { render, screen, fireEvent } from '@testing-library/react'
import { describe, it, expect, vi } from 'vitest'
import { DarkModeToggle } from '@/components/ui/DarkModeToggle'
import { DarkModeProvider } from '@/contexts/DarkModeContext'

describe('DarkModeToggle', () => {
  it('renders toggle button', () => {
    render(
      <DarkModeProvider>
        <DarkModeToggle />
      </DarkModeProvider>
    )
    const btn = screen.getByTitle('Dark mode')
    expect(btn).toBeInTheDocument()
  })

  it('toggles dark mode on click', () => {
    render(
      <DarkModeProvider>
        <DarkModeToggle />
      </DarkModeProvider>
    )
    const btn = screen.getByTitle('Dark mode')
    fireEvent.click(btn)
    expect(screen.getByTitle('Light mode')).toBeInTheDocument()
  })
})
