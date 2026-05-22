import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { Header } from '@/components/layout/Header'

vi.mock('@/lib/api')
import api from '@/lib/api'

const mockPush = vi.fn()
vi.mock('next/navigation', () => ({ useRouter: () => ({ push: mockPush }) }))

describe('Header', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders school name and sign out button', () => {
    render(<Header />)
    expect(screen.getByText('School Management System')).toBeInTheDocument()
    expect(screen.getByText('Sign Out')).toBeInTheDocument()
  })

  it('calls logout and redirects on sign out', async () => {
    ;(api.post as any).mockResolvedValueOnce({ data: { success: true } })
    render(<Header />)
    fireEvent.click(screen.getByText('Sign Out'))
    await waitFor(() => {
      expect(api.post).toHaveBeenCalledWith('/auth/logout')
    })
    expect(mockPush).toHaveBeenCalledWith('/login')
  })
})
