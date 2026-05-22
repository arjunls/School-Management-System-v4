import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { StudentFormModal } from '@/components/students/StudentFormModal'

vi.mock('@/lib/api')
import api from '@/lib/api'

function getForm(): HTMLFormElement {
  return screen.getByRole('button', { name: /create|update/i }).closest('form')!
}

describe('StudentFormModal', () => {
  const onClose = vi.fn()
  const onSuccess = vi.fn()

  beforeEach(() => {
    vi.clearAllMocks()
    ;(api.get as any).mockResolvedValue({ data: { success: true, data: [] } })
  })

  it('renders nothing when closed', () => {
    const { container } = render(
      <StudentFormModal open={false} onClose={onClose} onSuccess={onSuccess} />
    )
    expect(container.innerHTML).toBe('')
  })

  it('renders form when open', async () => {
    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} />)
    expect(screen.getByText('Add Student')).toBeInTheDocument()
    expect(screen.getByText('Create Student')).toBeInTheDocument()
  })

  it('renders edit mode with student data', () => {
    const student = { id: 1, name: 'John', email: 'john@test.com', status: 'active', kelas_id: 1 }
    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} student={student} />)
    expect(screen.getByText('Edit Student')).toBeInTheDocument()
    expect(screen.getByDisplayValue('John')).toBeInTheDocument()
    expect(screen.getByDisplayValue('john@test.com')).toBeInTheDocument()
  })

  it('calls create API on submit', async () => {
    ;(api.post as any).mockResolvedValueOnce({ data: { success: true, data: { id: 1 } } })

    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} />)
    fireEvent.submit(getForm())

    await waitFor(() => {
      expect(api.post).toHaveBeenCalled()
      expect(onSuccess).toHaveBeenCalledWith('Student created successfully')
      expect(onClose).toHaveBeenCalled()
    })
  })

  it('calls update API on submit in edit mode', async () => {
    ;(api.put as any).mockResolvedValueOnce({ data: { success: true } })

    const student = { id: 1, name: 'John', email: 'john@test.com', status: 'active' }
    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} student={student} />)

    fireEvent.submit(getForm())

    await waitFor(() => {
      expect(api.put).toHaveBeenCalled()
      expect(onSuccess).toHaveBeenCalledWith('Student updated successfully')
      expect(onClose).toHaveBeenCalled()
    })
  })

  it('shows validation errors from API', async () => {
    ;(api.post as any).mockRejectedValueOnce({
      response: {
        data: {
          errors: { name: ['The name field is required.'] },
        },
      },
    })

    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} />)
    fireEvent.submit(getForm())

    await waitFor(() => {
      expect(screen.getByText('The name field is required.')).toBeInTheDocument()
    })
  })

  it('shows general error from API', async () => {
    ;(api.post as any).mockRejectedValueOnce({
      response: { data: { message: 'Server error' } },
    })

    render(<StudentFormModal open onClose={onClose} onSuccess={onSuccess} />)
    fireEvent.submit(getForm())

    await waitFor(() => {
      expect(screen.getByText('Server error')).toBeInTheDocument()
    })
  })
})
