import { render, screen } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { ProtectedRoute } from '@/components/auth/ProtectedRoute'

const mockReplace = vi.fn()
vi.mock('next/navigation', () => ({ useRouter: () => ({ replace: mockReplace }) }))

let mockUser: any = null
let mockLoading = false
vi.mock('@/contexts/AuthContext', () => ({ useAuth: () => ({ user: mockUser, loading: mockLoading }) }))

function setAuth(user: any, loading: boolean) {
  mockUser = user
  mockLoading = loading
}

describe('ProtectedRoute', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    setAuth(null, false)
  })

  it('shows loader when loading', () => {
    setAuth(null, true)
    render(<ProtectedRoute><div>Content</div></ProtectedRoute>)
    expect(screen.getByText('Memuat...')).toBeInTheDocument()
    expect(screen.queryByText('Content')).not.toBeInTheDocument()
  })

  it('redirects to login when no user', () => {
    render(<ProtectedRoute><div>Content</div></ProtectedRoute>)
    expect(mockReplace).toHaveBeenCalledWith('/login')
  })

  it('renders children when authenticated', () => {
    setAuth({ id: 1, name: 'A', role: 'admin' }, false)
    render(<ProtectedRoute><div>Content</div></ProtectedRoute>)
    expect(screen.getByText('Content')).toBeInTheDocument()
  })

  it('redirects to dashboard when role not allowed', () => {
    setAuth({ id: 1, name: 'A', role: 'student' }, false)
    render(<ProtectedRoute roles={['admin']}><div>Content</div></ProtectedRoute>)
    expect(mockReplace).toHaveBeenCalledWith('/dashboard')
  })

  it('renders children when role matches', () => {
    setAuth({ id: 1, name: 'A', role: 'admin' }, false)
    render(<ProtectedRoute roles={['admin', 'teacher']}><div>Content</div></ProtectedRoute>)
    expect(screen.getByText('Content')).toBeInTheDocument()
  })
})
