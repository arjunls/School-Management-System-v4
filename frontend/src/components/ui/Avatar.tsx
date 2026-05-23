interface AvatarProps {
  name: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
  src?: string;
  status?: 'online' | 'offline' | 'away';
  className?: string;
}

const sizes = {
  sm: 'size-7 text-[10px]',
  md: 'size-8 text-xs',
  lg: 'size-10 text-sm',
  xl: 'size-14 text-lg',
};

const statusSizes = {
  sm: 'size-2',
  md: 'size-2.5',
  lg: 'size-3',
  xl: 'size-3.5',
};

const statusColors = {
  online: 'bg-emerald-500',
  offline: 'bg-gray-400',
  away: 'bg-amber-400',
};

export function Avatar({ name, size = 'md', src, status, className = '' }: AvatarProps) {
  const initials = name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);

  if (src) {
    return (
      <div className={`relative shrink-0 ${sizes[size]} ${className}`}>
        <img
          src={src}
          alt={name}
          className="size-full rounded-full object-cover"
        />
        {status && (
          <span className={`absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-background ${statusColors[status]} ${statusSizes[size]}`} />
        )}
      </div>
    );
  }

  return (
    <div className={`relative shrink-0 ${sizes[size]} ${className}`}>
      <div className="size-full rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium shadow-sm">
        {initials}
      </div>
      {status && (
        <span className={`absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-background ${statusColors[status]} ${statusSizes[size]}`} />
      )}
    </div>
  );
}
