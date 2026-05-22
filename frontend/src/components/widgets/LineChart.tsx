"use client";
import React from 'react';

interface LineChartProps {
  labels: string[];
  datasets: Record<string, (number | null)[]>;
  height?: number;
}

export function LineChart({ labels, datasets, height = 220 }: LineChartProps) {
  const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
  const colorKeys = Object.keys(datasets);
  if (colorKeys.length === 0) return <div className="flex h-48 items-center justify-center text-gray-400">No data</div>;

  const allValues = Object.values(datasets).flat().filter((v): v is number => v !== null);
  const max = Math.max(...allValues, 1);
  const min = Math.min(...allValues, 0);
  const range = max - min || 1;
  const w = Math.max(60, 600 / labels.length);
  const totalW = w * labels.length;
  const chartH = height - 40;

  const points = (data: (number | null)[]) =>
    data.map((v, i) => {
      const x = i * w + w / 2;
      const y = v !== null ? chartH - ((v - min) / range) * chartH + 10 : null;
      return y !== null ? `${i === 0 ? 'M' : 'L'}${x},${y}` : null;
    }).filter(Boolean).join(' ');

  return (
    <svg width="100%" height={height} viewBox={`0 0 ${Math.max(totalW, 200)} ${height}`} preserveAspectRatio="xMidYMid meet">
      {Object.entries(datasets).map(([label, data], idx) => {
        const path = points(data);
        return (
          <g key={label}>
            <path d={path} fill="none" stroke={colors[idx % colors.length]} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            {data.map((v, i) => {
              if (v === null) return null;
              const x = i * w + w / 2;
              const y = chartH - ((v - min) / range) * chartH + 10;
              return <circle key={i} cx={x} cy={y} r="3" fill={colors[idx % colors.length]}><title>{label}: {v}</title></circle>;
            })}
          </g>
        );
      })}
      {labels.map((l, i) => (
        <text key={i} x={i * w + w / 2} y={height - 5} textAnchor="middle" fontSize="9" fill="#6b7280">{l.length > 8 ? l.slice(0, 8) + '…' : l}</text>
      ))}
    </svg>
  );
}
