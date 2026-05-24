import React from 'react';
import Link from 'next/link';

interface Breadcrumb {
  label: string;
  href?: string;
}

interface PageHeaderProps {
  title: string;
  description?: string;
  breadcrumbs?: Breadcrumb[];
  action?: React.ReactNode;
  accent?: string;
  gradient?: boolean;
}

export function PageHeader({ title, description, breadcrumbs, action, accent, gradient }: PageHeaderProps) {
  return (
    <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div className="min-w-0">
        {breadcrumbs && breadcrumbs.length > 0 && (
          <nav className="flex items-center gap-1.5 text-xs text-muted-foreground mb-2">
            {breadcrumbs.map((crumb, i) => (
              <React.Fragment key={i}>
                {i > 0 && (
                  <svg className="size-3 text-muted-foreground/20 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                  </svg>
                )}
                {crumb.href ? (
                  <Link href={crumb.href} className="hover:text-foreground transition-colors shrink-0 font-medium">{crumb.label}</Link>
                ) : (
                  <span className="text-foreground font-semibold truncate max-w-[200px]">{crumb.label}</span>
                )}
              </React.Fragment>
            ))}
          </nav>
        )}
        <div className="flex items-start gap-3">
          {accent && <div className="w-0.5 h-8 rounded-full shrink-0 mt-0.5" style={{ background: `linear-gradient(to bottom, ${accent}, ${accent}33)` }} />}
          <div className="min-w-0">
            <h1 className={`text-2xl font-bold tracking-tight ${gradient ? 'text-gradient' : 'text-foreground'}`}>{title}</h1>
            {description && <p className="text-sm text-muted-foreground mt-1 leading-relaxed">{description}</p>}
          </div>
        </div>
      </div>
      {action && <div className="flex items-center gap-2 shrink-0">{action}</div>}
    </div>
  );
}
