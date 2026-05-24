import type { ReactNode } from 'react';

interface CardProps {
  children: ReactNode;
  className?: string;
  accent?: string;
  hover?: boolean;
  padding?: 'none' | 'sm' | 'md' | 'lg';
}

const paddings = {
  none: '',
  sm: 'p-4',
  md: 'p-5',
  lg: 'p-7',
};

export function Card({ children, className = '', accent, hover = false, padding = 'md' }: CardProps) {
  const hoverClass = hover
    ? 'transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md'
    : '';

  return (
    <div className={`relative rounded-xl border border-border bg-card text-card-foreground shadow-xs ${paddings[padding]} ${hoverClass} ${className}`}>
      {accent && (
        <div className="absolute top-0 left-0 right-0 h-0.5 rounded-t-xl" style={{ backgroundColor: accent }} />
      )}
      {children}
    </div>
  );
}

export function CardHeader({ title, description, action, className = '' }: {
  title: string;
  description?: string;
  action?: ReactNode;
  className?: string;
}) {
  return (
    <div className={`flex items-start justify-between gap-4 ${className}`}>
      <div className="min-w-0">
        <h3 className="text-base font-semibold text-foreground tracking-tight">{title}</h3>
        {description && <p className="text-sm text-muted-foreground mt-0.5 leading-relaxed">{description}</p>}
      </div>
      {action && <div className="shrink-0">{action}</div>}
    </div>
  );
}
