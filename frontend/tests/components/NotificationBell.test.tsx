import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { NotificationBell } from '@/components/notifications/NotificationBell'

vi.mock('@/lib/api')
import api from '@/lib/api'

describe('NotificationBell', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('shows no notifications when empty', async () => {
    ;(api.get as any).mockResolvedValueOnce({
      data: { success: true, data: { count: 0, notifications: [] } },
    })

    render(<NotificationBell />)
    fireEvent.click(screen.getByRole('button'))

    await waitFor(() => {
      expect(screen.getByText('No new notifications')).toBeInTheDocument()
    })
  })

  it('shows notification count badge', async () => {
    ;(api.get as any).mockResolvedValueOnce({
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
    ;(api.get as any).mockResolvedValueOnce({
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
    ;(api.get as any).mockResolvedValueOnce({
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
    ;(api.post as any).mockResolvedValueOnce({ data: { success: true } })

    render(<NotificationBell />)
    fireEvent.click(screen.getByRole('button'))

    await waitFor(() => {
      expect(screen.getByText('Mark all as read')).toBeInTheDocument()
    })

    ;(api.get as any).mockResolvedValueOnce({
      data: { success: true, data: { count: 0, notifications: [] } },
    })

    fireEvent.click(screen.getByText('Mark all as read'))

    await waitFor(() => {
      expect(api.post).toHaveBeenCalledWith('/notifications/mark-all-read')
    })
  })
})
