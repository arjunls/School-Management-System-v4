import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { ClassFormModal } from '@/components/classes/ClassFormModal'

vi.mock('@/lib/api')
import api from '@/lib/api'

describe('ClassFormModal', () => {
  const onClose = vi.fn()
  const onSuccess = vi.fn()

  beforeEach(() => { vi.clearAllMocks() })

  it('renders nothing when closed', () => {
    const { container } = render(<ClassFormModal open={false} onClose={onClose} onSuccess={onSuccess} />)
    expect(container.innerHTML).toBe('')
  })

  it('renders form when open', () => {
    render(<ClassFormModal open onClose={onClose} onSuccess={onSuccess} />)
    expect(screen.getByText('Add Class')).toBeInTheDocument()
    expect(screen.getByText('Create Class')).toBeInTheDocument()
  })

  it('renders edit mode with class data', () => {
    render(<ClassFormModal open onClose={onClose} onSuccess={onSuccess} classData={{ id: 1, name: 'X A', grade_level: 10, capacity: 30 }} />)
    expect(screen.getByText('Edit Class')).toBeInTheDocument()
    expect(screen.getByDisplayValue('X A')).toBeInTheDocument()
  })

  it('calls create API on submit', async () => {
    ;(api.post as any).mockResolvedValueOnce({ data: { success: true } })

    render(<ClassFormModal open onClose={onClose} onSuccess={onSuccess} />)
    const form = screen.getByRole('button', { name: 'Create Class' }).closest('form')!
    fireEvent.submit(form)

    await waitFor(() => {
      expect(api.post).toHaveBeenCalledWith('/classes', expect.any(Object))
      expect(onSuccess).toHaveBeenCalledWith('Class created')
      expect(onClose).toHaveBeenCalled()
    })
  })

  it('calls update API on submit in edit mode', async () => {
    ;(api.put as any).mockResolvedValueOnce({ data: { success: true } })

    render(<ClassFormModal open onClose={onClose} onSuccess={onSuccess} classData={{ id: 1, name: 'X A', grade_level: 10, capacity: 30 }} />)
    const form = screen.getByRole('button', { name: 'Update Class' }).closest('form')!
    fireEvent.submit(form)

    await waitFor(() => {
      expect(api.put).toHaveBeenCalledWith('/classes/1', expect.any(Object))
      expect(onSuccess).toHaveBeenCalledWith('Class updated')
      expect(onClose).toHaveBeenCalled()
    })
  })

  it('shows validation errors from API', async () => {
    ;(api.post as any).mockRejectedValueOnce({
      response: { data: { errors: { name: ['The name field is required.'] } } },
    })

    render(<ClassFormModal open onClose={onClose} onSuccess={onSuccess} />)
    const form = screen.getByRole('button', { name: 'Create Class' }).closest('form')!
    fireEvent.submit(form)

    await waitFor(() => {
      expect(screen.getByText('The name field is required.')).toBeInTheDocument()
    })
  })
})
