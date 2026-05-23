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

  it('renders sign in form', async () => {
    render(<LoginPage />)
    await screen.findByText('Masukkan kredensial akun Anda')
    expect(screen.getByLabelText('Email')).toBeInTheDocument()
    expect(screen.getByLabelText('Password')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: /masuk/i })).toBeInTheDocument()
  })

  it('shows demo account information', async () => {
    render(<LoginPage />)
    await screen.findByTitle('Login sebagai Admin')
    expect(screen.getByTitle('Login sebagai Guru')).toBeInTheDocument()
    expect(screen.getByTitle('Login sebagai Siswa')).toBeInTheDocument()
  })

  it('has forgot password link', async () => {
    render(<LoginPage />)
    await screen.findByText('Lupa password?')
    const link = screen.getByText('Lupa password?')
    expect(link).toHaveAttribute('href', '/forgot-password')
  })
})
