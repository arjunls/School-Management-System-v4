interface SkeletonProps {
  variant?: 'text' | 'card' | 'avatar' | 'badge' | 'chart' | 'table-row';
  className?: string;
}

const variants = {
  text: 'h-4 w-full rounded',
  card: 'h-32 w-full rounded-xl',
  avatar: 'size-10 rounded-full',
  badge: 'h-5 w-16 rounded-full',
  chart: 'h-48 w-full rounded-lg',
  'table-row': 'h-10 w-full rounded',
};

export function Skeleton({ variant = 'text', className = '' }: SkeletonProps) {
  return (
    <div
      className={`animate-pulse bg-muted/50 ${variants[variant]} ${className}`}
      aria-hidden="true"
    />
  );
}

export function TableSkeleton({ rows = 5, cols = 4 }: { rows?: number; cols?: number }) {
  return (
    <div className="space-y-3">
      <div className="flex gap-4">
        {Array.from({ length: cols }).map((_, i) => (
          <div key={i} className="flex-1">
            <Skeleton variant="text" className="h-3 w-3/4" />
          </div>
        ))}
      </div>
      {Array.from({ length: rows }).map((_, r) => (
        <div key={r} className="flex gap-4">
          {Array.from({ length: cols }).map((_, c) => (
            <div key={c} className="flex-1">
              <Skeleton variant="text" className={c === 0 ? 'w-2/3' : 'w-1/2'} />
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}
