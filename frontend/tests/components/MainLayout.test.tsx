import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { MainLayout } from '@/components/layout/MainLayout'

const mockPush = vi.fn()
const mockUsePathname = vi.fn(() => '/dashboard')
vi.mock('next/navigation', () => ({ useRouter: () => ({ push: mockPush }), usePathname: () => mockUsePathname() }))

let mockUser: any = null
vi.mock('@/contexts/AuthContext', () => ({
  useAuth: () => {
    const user = mockUser
    return {
      user,
      isAdmin: user?.role === 'admin',
      logout: vi.fn().mockResolvedValue(undefined),
      loading: false,
      isAuthenticated: !!user,
    }
  },
}))

describe('MainLayout', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockUser = null
  })

  it('renders sidebar with logo', () => {
    mockUser = { id: 1, name: 'Admin', role: 'admin' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.getByText('School Management')).toBeInTheDocument()
    expect(screen.getByText('Content')).toBeInTheDocument()
  })

  it('shows user role and name in header', () => {
    mockUser = { id: 1, name: 'John Doe', role: 'teacher' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.getByText(/teacher/)).toBeInTheDocument()
    expect(screen.getByText(/John Doe/)).toBeInTheDocument()
  })

  it('shows admin-only nav items for admin role', () => {
    mockUser = { id: 1, name: 'Admin', role: 'admin' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.getByText('Guru')).toBeInTheDocument()
  })

  it('hides admin-only nav items for student role', () => {
    mockUser = { id: 1, name: 'Student', role: 'student' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.queryByText('Guru')).not.toBeInTheDocument()
  })

  it('shows common nav items for student role', () => {
    mockUser = { id: 1, name: 'Student', role: 'student' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.getAllByText('Dashboard').length).toBeGreaterThanOrEqual(1)
    expect(screen.getByText('Tugas')).toBeInTheDocument()
    expect(screen.getByText('Kuis')).toBeInTheDocument()
    expect(screen.getByText('Perpustakaan')).toBeInTheDocument()
    expect(screen.getByText('Pesan')).toBeInTheDocument()
  })

  it('shows sign out button', () => {
    mockUser = { id: 1, name: 'User', role: 'admin' }
    render(<MainLayout><div>Content</div></MainLayout>)
    expect(screen.getAllByText('Keluar').length).toBeGreaterThanOrEqual(1)
  })
})
