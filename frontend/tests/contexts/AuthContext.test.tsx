import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'

vi.mock('@/lib/api')
import { mockPost, mockGet } from '@/lib/api'

const mockPush = vi.fn()
vi.mock('next/navigation', () => ({ useRouter: () => ({ push: mockPush }) }))

import { AuthProvider, useAuth } from '@/contexts/AuthContext'

function TestConsumer() {
  const { user, loading, login, logout, isAuthenticated, isAdmin } = useAuth()
  const handleLogin = () => { login('a@b.com', 'pass').catch(() => {}) }
  return (
    <div>
      <span data-testid="loading">{String(loading)}</span>
      <span data-testid="auth">{String(isAuthenticated)}</span>
      <span data-testid="admin">{String(isAdmin)}</span>
      {user && <span data-testid="user-name">{user.name}</span>}
      <button data-testid="btn-login" onClick={handleLogin}>Login</button>
      <button data-testid="btn-logout" onClick={() => logout()}>Logout</button>
    </div>
  )
}

describe('AuthContext', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.stubGlobal('localStorage', {
      getItem: vi.fn(() => null),
      setItem: vi.fn(),
      removeItem: vi.fn(),
    })
    Object.defineProperty(document, 'cookie', { value: '', writable: true })
    mockPush.mockClear()
    vi.spyOn(console, 'error').mockImplementation(() => {})
  })

  afterEach(() => {
    vi.unstubAllGlobals()
    vi.restoreAllMocks()
  })

  it('renders with no user initially', async () => {
    render(
      <AuthProvider>
        <TestConsumer />
      </AuthProvider>
    )
    await waitFor(() => {
      expect(screen.getByTestId('auth').textContent).toBe('false')
    })
  })

  it('login sets user and redirects', async () => {
    mockPost.mockResolvedValueOnce({
      data: { success: true, data: { user: { id: 1, name: 'Admin', email: 'a@b.com', role: 'admin' }, token: 'abc' } },
    })

    render(
      <AuthProvider>
        <TestConsumer />
      </AuthProvider>
    )

    fireEvent.click(screen.getByTestId('btn-login'))

    await waitFor(() => {
      expect(screen.getByTestId('user-name').textContent).toBe('Admin')
    })
    expect(screen.getByTestId('auth').textContent).toBe('true')
    expect(screen.getByTestId('admin').textContent).toBe('true')
    expect(mockPush).toHaveBeenCalledWith('/dashboard')
  })

  it('login sets token in localStorage', async () => {
    const setItem = vi.fn()
    vi.stubGlobal('localStorage', { getItem: vi.fn(), setItem, removeItem: vi.fn() })

    mockPost.mockResolvedValueOnce({
      data: { success: true, data: { user: { id: 1, name: 'A', role: 'student' }, token: 'my-token' } },
    })

    render(
      <AuthProvider>
        <TestConsumer />
      </AuthProvider>
    )

    fireEvent.click(screen.getByTestId('btn-login'))
    await waitFor(() => expect(screen.getByTestId('auth').textContent).toBe('true'))
    expect(setItem).toHaveBeenCalledWith('access_token', 'my-token')
  })

  it('logout clears user and redirects', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })

    render(
      <AuthProvider>
        <TestConsumer />
      </AuthProvider>
    )

    fireEvent.click(screen.getByTestId('btn-logout'))
    await waitFor(() => expect(screen.getByTestId('auth').textContent).toBe('false'))
    expect(mockPush).toHaveBeenCalledWith('/login')
  })

  it('login failure does not set user', async () => {
    mockPost.mockRejectedValueOnce(new Error('Invalid'))

    render(
      <AuthProvider>
        <TestConsumer />
      </AuthProvider>
    )

    fireEvent.click(screen.getByTestId('btn-login'))
    await waitFor(() => expect(screen.getByTestId('loading').textContent).toBe('false'))
    expect(screen.getByTestId('auth').textContent).toBe('false')
  })
})
