import { render, screen } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import { SkeletonLoader, SkeletonText, SkeletonCard } from '@/components/ui/SkeletonLoader'

describe('SkeletonLoader', () => {
  it('renders with default props', () => {
    const { container } = render(<SkeletonLoader />)
    const el = container.firstChild as HTMLElement
    expect(el).toHaveClass('animate-pulse')
    expect(el).toHaveStyle({ height: '16px', width: '100%' })
  })

  it('renders with custom height and width', () => {
    const { container } = render(<SkeletonLoader height={40} width={200} />)
    const el = container.firstChild as HTMLElement
    expect(el).toHaveStyle({ height: '40px', width: '200px' })
  })
})

describe('SkeletonText', () => {
  it('renders default 3 lines', () => {
    const { container } = render(<SkeletonText />)
    expect(container.children[0].children).toHaveLength(3)
  })

  it('renders custom number of lines', () => {
    const { container } = render(<SkeletonText lines={5} />)
    expect(container.children[0].children).toHaveLength(5)
  })
})

describe('SkeletonCard', () => {
  it('renders successfully', () => {
    const { container } = render(<SkeletonCard />)
    expect(container.firstChild).toBeInTheDocument()
  })
})
