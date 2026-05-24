import api from './client';

export const dashboardAPI = {
  getStats: (params?: Record<string, any>) => api.get('/dashboard/stats', { params }),

  getAttendanceChartData: (params?: Record<string, any>) => api.get('/dashboard/attendance-chart', { params }),

  getPerformanceChartData: (params?: Record<string, any>) => api.get('/dashboard/performance-chart', { params }),

  getStudentPerformanceTrend: (studentId: number, params?: Record<string, any>) =>
    api.get(`/dashboard/student-performance/${studentId}`, { params }),
};
