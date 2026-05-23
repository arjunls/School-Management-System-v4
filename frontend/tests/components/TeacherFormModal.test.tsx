import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { TeacherFormModal } from '@/components/teachers/TeacherFormModal'

vi.mock('@/lib/api')
import { mockPost, mockPut } from '@/lib/api'

describe('TeacherFormModal', () => {
  const onClose = vi.fn()
  const onSuccess = vi.fn()

  beforeEach(() => { vi.clearAllMocks() })

  it('renders nothing when closed', () => {
    const { container } = render(<TeacherFormModal open={false} onClose={onClose} onSuccess={onSuccess} />)
    expect(container.innerHTML).toBe('')
  })

  it('renders form when open', () => {
    render(<TeacherFormModal open onClose={onClose} onSuccess={onSuccess} />)
    expect(screen.getByText('Tambah Guru')).toBeInTheDocument()
    expect(screen.getByText('Simpan')).toBeInTheDocument()
  })

  it('renders edit mode', () => {
    const teacher = { id: 1, name: 'Mr. Smith', email: 'smith@school.com', status: 'active' }
    render(<TeacherFormModal open onClose={onClose} onSuccess={onSuccess} teacher={teacher} />)
    expect(screen.getByText('Edit Guru')).toBeInTheDocument()
    expect(screen.getByDisplayValue('Mr. Smith')).toBeInTheDocument()
  })

  it('calls create API on submit', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })

    render(<TeacherFormModal open onClose={onClose} onSuccess={onSuccess} />)
    const form = screen.getByRole('button', { name: 'Simpan' }).closest('form')!
    fireEvent.submit(form)

    await waitFor(() => {
      expect(mockPost).toHaveBeenCalled()
      expect(onSuccess).toHaveBeenCalledWith('Teacher created successfully')
      expect(onClose).toHaveBeenCalled()
    })
  })

  it('calls update API on submit in edit mode', async () => {
    mockPut.mockResolvedValueOnce({ data: { success: true } })

    const teacher = { id: 1, name: 'Mr. Smith', email: 'smith@school.com', status: 'active' }
    render(<TeacherFormModal open onClose={onClose} onSuccess={onSuccess} teacher={teacher} />)
    const form = screen.getByRole('button', { name: 'Perbarui' }).closest('form')!
    fireEvent.submit(form)

    await waitFor(() => {
      expect(mockPut).toHaveBeenCalled()
      expect(onSuccess).toHaveBeenCalledWith('Teacher updated successfully')
      expect(onClose).toHaveBeenCalled()
    })
  })
})
