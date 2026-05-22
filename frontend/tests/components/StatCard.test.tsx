import { render, screen } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import { StatCard } from '@/components/widgets/StatCard'

describe('StatCard', () => {
  it('renders title and value', () => {
    render(<StatCard title="Students" value={120} icon={<span>Icon</span>} color="blue" />)
    expect(screen.getByText('Students')).toBeInTheDocument()
    expect(screen.getByText('120')).toBeInTheDocument()
  })

  it('renders trend when provided', () => {
    render(
      <StatCard
        title="Revenue"
        value="$5,000"
        icon={<span>$</span>}
        color="green"
        trend={{ value: '12%', isPositive: true }}
      />
    )
    expect(screen.getByText(/12%/)).toBeInTheDocument()
  })

  it('shows trend down when negative', () => {
    render(
      <StatCard
        title="Dropouts"
        value={5}
        icon={<span>↓</span>}
        color="red"
        trend={{ value: '3%', isPositive: false }}
      />
    )
    expect(screen.getByText(/3%/)).toBeInTheDocument()
  })
})
