"use client";
import React, { useEffect, useState, useCallback } from 'react';
import { scheduleAPI, classAPI } from '@/lib/api';
import { MainLayout } from '@/components/layout/MainLayout';
import { ProtectedRoute } from '@/components/auth/ProtectedRoute';

interface ScheduleItem {
  id: number; class_id: number; subject_id: number; teacher_id?: number | null;
  day_of_week: string; start_time: string; end_time: string; room?: string | null;
  subject?: { id: number; name: string; code: string } | null;
  teacher?: { id: number; name: string } | null;
}

interface Kelas { id: number; name: string; grade_level: number; }

const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
const DAY_LABELS: Record<string, string> = {
  monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday',
  thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday',
};

const COLORS = [
  'bg-blue-100 border-blue-300 text-blue-800',
  'bg-green-100 border-green-300 text-green-800',
  'bg-purple-100 border-purple-300 text-purple-800',
  'bg-amber-100 border-amber-300 text-amber-800',
  'bg-pink-100 border-pink-300 text-pink-800',
  'bg-teal-100 border-teal-300 text-teal-800',
  'bg-orange-100 border-orange-300 text-orange-800',
  'bg-indigo-100 border-indigo-300 text-indigo-800',
];

function toMinutes(t: string): number {
  const [h, m] = t.split(':').map(Number);
  return h * 60 + m;
}

const HOUR_HEIGHT = 80;
const START_HOUR = 7;
const END_HOUR = 17;

export default function TimetablePage() {
  const [schedules, setSchedules] = useState<ScheduleItem[]>([]);
  const [classes, setClasses] = useState<Kelas[]>([]);
  const [selectedClassId, setSelectedClassId] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const res = await classAPI.getList();
        const body = res.data as { success?: boolean; data?: unknown[] };
        const list = (Array.isArray(body?.data) ? body.data : []) as Kelas[];
        setClasses(list);
        if (list.length > 0) setSelectedClassId(String(list[0].id));
      } catch { /* */ }
    })();
  }, []);

  const fetchSchedules = useCallback(async () => {
    if (!selectedClassId) return;
    setLoading(true);
    try {
      const params: Record<string, any> = {};
      if (selectedClassId) params.class_id = selectedClassId;
      const res = await scheduleAPI.getList(params);
      const body = res.data as { success?: boolean; data?: unknown[] };
      const items = (Array.isArray(body?.data) ? body.data : []) as ScheduleItem[];

      items.sort((a, b) => {
        const da = DAYS.indexOf(a.day_of_week);
        const db = DAYS.indexOf(b.day_of_week);
        if (da !== db) return da - db;
        return toMinutes(a.start_time) - toMinutes(b.start_time);
      });

      setSchedules(items);
    } catch { /* */ }
    finally { setLoading(false); }
  }, [selectedClassId]);

  useEffect(() => { fetchSchedules(); }, [fetchSchedules]);

  const totalMinutes = (END_HOUR - START_HOUR) * 60;
  const gridHeight = (totalMinutes / 60) * HOUR_HEIGHT;

  const subjectColorMap = new Map<string, string>();
  let colorIdx = 0;
  for (const s of schedules) {
    const key = s.subject?.code || String(s.subject_id);
    if (!subjectColorMap.has(key)) {
      subjectColorMap.set(key, COLORS[colorIdx % COLORS.length]);
      colorIdx++;
    }
  }

  return (
    <ProtectedRoute roles={['admin', 'teacher', 'student']}>
      <MainLayout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Timetable</h1>
            <div className="flex items-center gap-2">
              <label className="text-sm font-medium text-gray-700">Class:</label>
              <select
                className="rounded-md border-0 px-3 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                value={selectedClassId}
                onChange={(e) => setSelectedClassId(e.target.value)}
              >
                {classes.map((c) => <option key={c.id} value={c.id}>{c.name} (Grade {c.grade_level})</option>)}
              </select>
            </div>
          </div>

          {loading ? (
            <div className="text-center py-12 text-gray-500">Loading timetable...</div>
          ) : schedules.length === 0 ? (
            <div className="text-center py-12 text-gray-500">No schedule for this class.</div>
          ) : (
            <div className="overflow-x-auto bg-white rounded-lg shadow border">
              <div className="min-w-[900px]">
                {/* Header row */}
                <div className="grid grid-cols-[80px_repeat(6,1fr)] border-b border-gray-200 bg-gray-50 sticky top-0">
                  <div className="px-3 py-3 text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Time</div>
                  {DAYS.map((d) => (
                    <div key={d} className="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 last:border-r-0">
                      {DAY_LABELS[d]}
                    </div>
                  ))}
                </div>

                {/* Time grid */}
                <div className="grid grid-cols-[80px_repeat(6,1fr)] relative" style={{ height: gridHeight }}>
                  {/* Time labels */}
                  <div className="border-r border-gray-200">
                    {Array.from({ length: END_HOUR - START_HOUR }, (_, i) => {
                      const hour = START_HOUR + i;
                      return (
                        <div key={hour} className="border-b border-gray-100 px-2 py-1 text-xs text-gray-500" style={{ height: HOUR_HEIGHT }}>
                          {String(hour).padStart(2, '0')}:00
                        </div>
                      );
                    })}
                  </div>

                  {/* Day columns */}
                  {DAYS.map((day, dayIdx) => (
                    <div key={day} className={`relative border-r border-gray-200 ${dayIdx === 5 ? 'border-r-0' : ''}`}>
                      {/* Hour lines */}
                      {Array.from({ length: END_HOUR - START_HOUR }, (_, i) => (
                        <div key={i} className="border-b border-gray-100" style={{ height: HOUR_HEIGHT }} />
                      ))}

                      {/* Schedule blocks */}
                      {schedules
                        .filter((s) => s.day_of_week === day)
                        .map((s) => {
                          const startMin = toMinutes(s.start_time);
                          const endMin = toMinutes(s.end_time);
                          const top = ((startMin - START_HOUR * 60) / totalMinutes) * gridHeight;
                          const height = Math.max(((endMin - startMin) / totalMinutes) * gridHeight, 28);
                          const colorClass = subjectColorMap.get(s.subject?.code || String(s.subject_id)) || COLORS[0];

                          return (
                            <div
                              key={s.id}
                              className={`absolute left-0.5 right-0.5 rounded border px-2 py-1 text-xs overflow-hidden ${colorClass}`}
                              style={{ top, height, minHeight: 28, zIndex: 10 }}
                              title={`${s.subject?.name || ''}\n${s.teacher?.name || ''}\n${s.room || ''}`}
                            >
                              <p className="font-semibold truncate leading-tight">{s.subject?.name || `#${s.subject_id}`}</p>
                              <p className="truncate leading-tight opacity-75">{s.teacher?.name || ''}</p>
                              {s.room && <p className="truncate leading-tight opacity-60">{s.room}</p>}
                              <p className="leading-tight opacity-60">{s.start_time.slice(0,5)}–{s.end_time.slice(0,5)}</p>
                            </div>
                          );
                        })}
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* Subject color legend */}
          {schedules.length > 0 && (
            <div className="bg-white rounded-lg shadow border p-4">
              <h3 className="text-sm font-medium text-gray-700 mb-2">Subjects</h3>
              <div className="flex flex-wrap gap-3">
                {Array.from(subjectColorMap.entries()).map(([code, color]) => (
                  <span key={code} className={`inline-flex px-2.5 py-1 rounded text-xs font-medium ${color}`}>
                    {code}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </MainLayout>
    </ProtectedRoute>
  );
}
