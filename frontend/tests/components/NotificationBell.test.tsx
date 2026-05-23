import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { NotificationBell } from '@/components/notifications/NotificationBell'

vi.mock('@/lib/api')
import { mockGet, mockPost } from '@/lib/api'

describe('NotificationBell', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('shows no notifications when empty', async () => {
    mockGet.mockResolvedValueOnce({
      data: { success: true, data: { count: 0, notifications: [] } },
    })

    render(<NotificationBell />)
    fireEvent.click(screen.getByRole('button'))

    await waitFor(() => {
      expect(screen.getByText('Tidak ada notifikasi baru')).toBeInTheDocument()
    })
  })

  it('shows notification count badge', async () => {
    mockGet.mockResolvedValueOnce({
      data: {
        success: true,
        data: {
          count: 3,
          notifications: [
            { id: '1', data: { message: 'New message' }, read_at: null, created_at: '2026-05-21T10:00:00Z' },
          ],
        },
      },
    })

    render(<NotificationBell />)
    const badge = await screen.findByText('3')
    expect(badge).toBeInTheDocument()
  })

  it('shows notification message in dropdown', async () => {
    mockGet.mockResolvedValueOnce({
      data: {
        success: true,
        data: {
          count: 1,
          notifications: [
            { id: '1', data: { message: 'Assignment due' }, read_at: null, created_at: '2026-05-21T10:00:00Z' },
          ],
        },
      },
    })

    render(<NotificationBell />)
    fireEvent.click(screen.getByRole('button'))

    await waitFor(() => {
      expect(screen.getByText('Assignment due')).toBeInTheDocument()
    })
  })

  it('calls markAllAsRead when button clicked', async () => {
    mockGet.mockResolvedValueOnce({
      data: {
        success: true,
        data: {
          count: 2,
          notifications: [
            { id: '1', data: { message: 'N1' }, read_at: null, created_at: '2026-05-21T10:00:00Z' },
            { id: '2', data: { message: 'N2' }, read_at: null, created_at: '2026-05-21T11:00:00Z' },
          ],
        },
      },
    })
    mockPost.mockResolvedValueOnce({ data: { success: true } })

    render(<NotificationBell />)
    fireEvent.click(screen.getByRole('button'))

    await waitFor(() => {
      expect(screen.getByText('Tandai sudah dibaca')).toBeInTheDocument()
    })

    mockGet.mockResolvedValueOnce({
      data: { success: true, data: { count: 0, notifications: [] } },
    })

    fireEvent.click(screen.getByText('Tandai sudah dibaca'))

    await waitFor(() => {
      expect(mockPost).toHaveBeenCalled()
    })
  })
})
