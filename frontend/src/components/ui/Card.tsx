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
  md: 'p-6',
  lg: 'p-8',
};

export function Card({ children, className = '', accent, hover = false, padding = 'md' }: CardProps) {
  return (
    <div
      className={`relative rounded-xl border border-border bg-card text-card-foreground shadow-xs ${paddings[padding]} ${hover ? 'transition-all duration-200 hover:shadow-md hover:shadow-card-hover' : ''} ${className}`}
    >
      {accent && (
        <div className="absolute top-0 left-0 w-full h-0.5 rounded-t-xl" style={{ background: accent }} />
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
    <div className={`flex items-start justify-between gap-4 mb-4 ${className}`}>
      <div>
        <h3 className="text-base font-semibold text-foreground">{title}</h3>
        {description && <p className="text-sm text-muted-foreground mt-0.5">{description}</p>}
      </div>
      {action && <div className="shrink-0">{action}</div>}
    </div>
  );
}
