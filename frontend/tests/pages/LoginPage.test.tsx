import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'

vi.mock('@/lib/api')
import { mockPost, mockGet } from '@/lib/api'

vi.mock('next/navigation', () => ({ useRouter: () => ({ push: vi.fn() }) }))
vi.mock('@/contexts/AuthContext', () => ({
  useAuth: () => ({
    login: vi.fn().mockResolvedValue(undefined),
    loading: false,
    user: null,
    isAuthenticated: false,
  }),
}))

import LoginPage from '@/app/login/page'

describe('LoginPage', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders sign in form', () => {
    render(<LoginPage />)
    expect(screen.getByText('Sign in to your account')).toBeInTheDocument()
    expect(screen.getByLabelText('Email Address')).toBeInTheDocument()
    expect(screen.getByLabelText('Password')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: /sign in/i })).toBeInTheDocument()
  })

  it('shows demo account information', () => {
    render(<LoginPage />)
    expect(screen.getByText(/admin@school.com/)).toBeInTheDocument()
    expect(screen.getByText(/teacher@school.com/)).toBeInTheDocument()
    expect(screen.getByText(/student@school.com/)).toBeInTheDocument()
  })

  it('has forgot password link', () => {
    render(<LoginPage />)
    const link = screen.getByText('Forgot password?')
    expect(link).toHaveAttribute('href', '/forgot-password')
  })
})
