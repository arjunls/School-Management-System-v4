import { render, screen, fireEvent, act } from '@testing-library/react'
import { describe, it, expect, vi } from 'vitest'
import { ToastProvider, useToast } from '@/components/ui/Toast'

function TestButton() {
  const { toast } = useToast()
  return <button onClick={() => toast('Hello!', 'success')}>Show Toast</button>
}

describe('Toast', () => {
  it('shows and dismisses toast', () => {
    vi.useFakeTimers()
    render(
      <ToastProvider>
        <TestButton />
      </ToastProvider>
    )

    fireEvent.click(screen.getByText('Show Toast'))
    expect(screen.getByText('Hello!')).toBeInTheDocument()

    act(() => { vi.advanceTimersByTime(4000) })
    expect(screen.queryByText('Hello!')).not.toBeInTheDocument()
    vi.useRealTimers()
  })

  it('dismisses on click', () => {
    render(
      <ToastProvider>
        <TestButton />
      </ToastProvider>
    )

    fireEvent.click(screen.getByText('Show Toast'))
    fireEvent.click(screen.getByText('Hello!'))
    expect(screen.queryByText('Hello!')).not.toBeInTheDocument()
  })
})
