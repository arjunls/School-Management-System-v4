import { render, screen, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'

vi.mock('@/lib/api')
import { mockGet, mockPost, mockDelete } from '@/lib/api'

vi.mock('next/navigation', () => ({ useRouter: () => ({ push: vi.fn() }), usePathname: () => '/students' }))

vi.mock('@/contexts/AuthContext', () => ({
  useAuth: () => ({
    user: { id: 1, name: 'Admin', role: 'admin' },
    loading: false,
    isAuthenticated: true,
    isAdmin: true,
    logout: vi.fn(),
  }),
}))

import { ToastProvider } from '@/components/ui/Toast'
import StudentsPage from '@/app/students/page'

function Wrapper({ children }: { children: React.ReactNode }) {
  return <ToastProvider>{children}</ToastProvider>
}

describe('StudentsPage', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockGet.mockResolvedValue({ data: { success: true, data: [] } })
  })

  it('renders page heading', async () => {
    render(<StudentsPage />, { wrapper: Wrapper })
    await waitFor(() => {
      expect(screen.getByRole('heading', { name: 'Siswa' })).toBeInTheDocument()
    })
  })

  it('shows add student button', async () => {
    render(<StudentsPage />, { wrapper: Wrapper })
    await waitFor(() => {
      expect(screen.getByRole('button', { name: /tambah/i })).toBeInTheDocument()
    })
  })

  it('renders student list from API', async () => {
    mockGet.mockResolvedValue({
      data: {
        success: true,
        data: [{ id: 1, name: 'John Doe', email: 'john@test.com', status: 'active', kelas: null }],
        pagination: { total: 1, per_page: 10, current_page: 1, last_page: 1, from: 1, to: 1 },
      },
    })

    render(<StudentsPage />, { wrapper: Wrapper })
    await waitFor(() => {
      expect(screen.getAllByText('John Doe').length).toBeGreaterThanOrEqual(1)
    })
    expect(screen.getByText('john@test.com')).toBeInTheDocument()
  })

  it('shows sidebar navigation', async () => {
    render(<StudentsPage />, { wrapper: Wrapper })
    await waitFor(() => {
      expect(screen.getAllByRole('link', { name: /dashboard/i }).length).toBeGreaterThanOrEqual(1)
    })
  })
})
