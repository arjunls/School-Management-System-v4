import api from './client';

export const reportAPI = {
  studentReportCard: (studentId: number) => api.get(`/reports/student-report-card/${studentId}`),
  attendanceReport: (params?: Record<string, any>) => api.get('/reports/attendance', { params }),
  transcript: (studentId: number) => api.get(`/reports/transcript/${studentId}`),
};

export const exportAPI = {
  download: async (type: string) => {
    const base = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
    const token = typeof window !== 'undefined' ? localStorage.getItem('access_token') : null;

    try {
      const res = await fetch(`${base}/export/${type}`, {
        headers: { Authorization: `Bearer ${token}`, Accept: 'text/csv' },
      });

      if (!res.ok) throw new Error('Export failed');

      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${type}-${new Date().toISOString().slice(0, 10)}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    } catch (e) {
      console.error('Export failed', e);
    }
  },
};
