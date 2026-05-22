import axios from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    if (typeof window !== 'undefined') {
      const token = localStorage.getItem('access_token');
      if (token) {
        if (!config.headers) {
          (config as any).headers = {};
        }
        // Attach bearer token for authenticated requests
        (config.headers as any).Authorization = `Bearer ${token}`;
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Handle 401 Unauthorized errors
    if (error.response?.status === 401) {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('access_token');
        document.cookie = 'access_token=; path=/; max-age=0';
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);

export default api;

// API service functions
export const authAPI = {
  login: (email: string, password: string) => 
    api.post('/auth/login', { email, password }),

  register: (data: { name: string; email: string; password: string; password_confirmation: string; role?: string }) =>
    api.post('/auth/register', data),
  
  logout: () => api.post('/auth/logout'),
  
  getProfile: () => api.get('/auth/profile'),

  refresh: () => api.post('/auth/refresh'),

  changePassword: (data: { current_password: string; password: string; password_confirmation: string }) =>
    api.post('/auth/change-password', data),

  forgotPassword: (email: string) =>
    api.post('/auth/forgot-password', { email }),

  resetPassword: (data: { token: string; email: string; password: string; password_confirmation: string }) =>
    api.post('/auth/reset-password', data),
};

export const dashboardAPI = {
  getStats: (params?: Record<string, any>) => api.get('/dashboard/stats', { params }),
  
  getAttendanceChartData: (params?: Record<string, any>) => api.get('/dashboard/attendance-chart', { params }),
  
  getPerformanceChartData: (params?: Record<string, any>) => api.get('/dashboard/performance-chart', { params }),

  getStudentPerformanceTrend: (studentId: number, params?: Record<string, any>) =>
    api.get(`/dashboard/student-performance/${studentId}`, { params }),
};

export const studentAPI = {
  getList: (params: Record<string, any>) => 
    api.get('/students', { params }),
  
  getPaginated: (params: Record<string, any>) =>
    api.get('/students/paginated', { params }),
  
  getById: (id: string) => api.get(`/students/${id}`),
  
  create: (data: Record<string, any>) => 
    api.post('/students', data),
  
  update: (id: string, data: Record<string, any>) => 
    api.put(`/students/${id}`, data),
  
  delete: (id: string) => api.delete(`/students/${id}`),
};

export const parentAPI = {
  getChildren: () => api.get('/parents/children'),
  link: (data: { parent_id: number; student_id: number; relationship?: string }) =>
    api.post('/parents/link', data),
  unlink: (data: { parent_id: number; student_id: number }) =>
    api.post('/parents/unlink', data),
  getStudentParents: (studentId: number) =>
    api.get(`/parents/students/${studentId}/parents`),
  getStudentGrades: (studentId: number) =>
    api.get(`/parents/students/${studentId}/grades`),
};

export const teacherAPI = {
  getList: (params: Record<string, any>) => 
    api.get('/teachers', { params }),
  
  getPaginated: (params: Record<string, any>) =>
    api.get('/teachers/paginated', { params }),
  
  getById: (id: string) => api.get(`/teachers/${id}`),
  
  create: (data: Record<string, any>) => 
    api.post('/teachers', data),
  
  update: (id: string, data: Record<string, any>) => 
    api.put(`/teachers/${id}`, data),
  
  delete: (id: string) => api.delete(`/teachers/${id}`),
};

export const classAPI = {
  getList: (params?: Record<string, any>) => 
    api.get('/classes', { params }),
  
  getPaginated: (params: Record<string, any>) =>
    api.get('/classes/paginated', { params }),
  
  getById: (id: string) => api.get(`/classes/${id}`),
  
  create: (data: Record<string, any>) => 
    api.post('/classes', data),
  
  update: (id: string, data: Record<string, any>) => 
    api.put(`/classes/${id}`, data),
  
  delete: (id: string) => api.delete(`/classes/${id}`),
};

export const gradeAPI = {
  getList: (params: Record<string, any>) =>
    api.get('/grades', { params }),

  getPaginated: (params: Record<string, any>) =>
    api.get('/grades/paginated', { params }),

  create: (data: Record<string, any>) =>
    api.post('/grades', data),

  update: (id: string, data: Record<string, any>) =>
    api.put(`/grades/${id}`, data),

  delete: (id: string) => api.delete(`/grades/${id}`),
};

export const subjectAPI = {
  getList: (params?: Record<string, any>) =>
    api.get('/subjects', { params }),

  getPaginated: (params: Record<string, any>) =>
    api.get('/subjects/paginated', { params }),

  getById: (id: string) => api.get(`/subjects/${id}`),

  create: (data: Record<string, any>) =>
    api.post('/subjects', data),

  update: (id: string, data: Record<string, any>) =>
    api.put(`/subjects/${id}`, data),

  delete: (id: string) => api.delete(`/subjects/${id}`),
};

export const scheduleAPI = {
  getList: (params?: Record<string, any>) =>
    api.get('/schedules', { params }),

  getPaginated: (params: Record<string, any>) =>
    api.get('/schedules/paginated', { params }),

  getById: (id: string) => api.get(`/schedules/${id}`),

  create: (data: Record<string, any>) =>
    api.post('/schedules', data),

  update: (id: string, data: Record<string, any>) =>
    api.put(`/schedules/${id}`, data),

  delete: (id: string) => api.delete(`/schedules/${id}`),
};

export const attendanceAPI = {
  getList: (params: Record<string, any>) =>
    api.get('/attendance', { params }),

  getPaginated: (params: Record<string, any>) =>
    api.get('/attendance/paginated', { params }),

  create: (data: Record<string, any>) =>
    api.post('/attendance', data),

  update: (id: string, data: Record<string, any>) =>
    api.put(`/attendance/${id}`, data),

  delete: (id: string) => api.delete(`/attendance/${id}`),
};

export const libraryAPI = {
  getBooks: (params?: Record<string, any>) => api.get('/library/books', { params }),
  createBook: (data: Record<string, any>) => api.post('/library/books', data),
  updateBook: (id: number, data: Record<string, any>) => api.put(`/library/books/${id}`, data),
  deleteBook: (id: number) => api.delete(`/library/books/${id}`),
  getLoans: (params?: Record<string, any>) => api.get('/library/loans', { params }),
  createLoan: (data: Record<string, any>) => api.post('/library/loans', data),
  returnBook: (id: number) => api.post(`/library/loans/${id}/return`),
};

export const healthAPI = {
  get: (studentId: number) => api.get(`/health/${studentId}`),
  save: (studentId: number, data: Record<string, any>) => api.put(`/health/${studentId}`, data),
};

export const eventAPI = {
  getAll: (params?: Record<string, any>) => api.get('/events', { params }),
  create: (data: Record<string, any>) => api.post('/events', data),
  update: (id: number, data: Record<string, any>) => api.put(`/events/${id}`, data),
  delete: (id: number) => api.delete(`/events/${id}`),
};

export const announcementAPI = {
  getAll: (params?: Record<string, any>) => api.get('/announcements', { params }),
  create: (data: Record<string, any>) => api.post('/announcements', data),
  update: (id: number, data: Record<string, any>) => api.put(`/announcements/${id}`, data),
  delete: (id: number) => api.delete(`/announcements/${id}`),
};

export const quizAPI = {
  getAll: (params?: Record<string, any>) => api.get('/quizzes', { params }),
  get: (id: number) => api.get(`/quizzes/${id}`),
  create: (data: Record<string, any>) => api.post('/quizzes', data),
  update: (id: number, data: Record<string, any>) => api.put(`/quizzes/${id}`, data),
  delete: (id: number) => api.delete(`/quizzes/${id}`),
  addQuestion: (quizId: number, data: Record<string, any>) => api.post(`/quizzes/${quizId}/questions`, data),
  updateQuestion: (id: number, data: Record<string, any>) => api.put(`/quizzes/questions/${id}`, data),
  deleteQuestion: (id: number) => api.delete(`/quizzes/questions/${id}`),
  start: (quizId: number) => api.post(`/quizzes/${quizId}/start`),
  submit: (attemptId: number, data: Record<string, any>) => api.post(`/quizzes/attempts/${attemptId}/submit`, data),
  attempts: (params?: Record<string, any>) => api.get('/quizzes/attempts', { params }),
  gradeEssay: (attemptId: number, questionId: number, data: Record<string, any>) => api.post(`/quizzes/attempts/${attemptId}/grade/${questionId}`, data),
};

export const extracurricularAPI = {
  getAll: () => api.get('/extracurriculars'),
  create: (data: Record<string, any>) => api.post('/extracurriculars', data),
  update: (id: number, data: Record<string, any>) => api.put(`/extracurriculars/${id}`, data),
  delete: (id: number) => api.delete(`/extracurriculars/${id}`),
  join: (id: number) => api.post(`/extracurriculars/${id}/join`),
  leave: (id: number) => api.post(`/extracurriculars/${id}/leave`),
};

export const feeAPI = {
  getTypes: () => api.get('/fees/types'),
  createType: (data: Record<string, any>) => api.post('/fees/types', data),
  updateType: (id: number, data: Record<string, any>) => api.put(`/fees/types/${id}`, data),
  deleteType: (id: number) => api.delete(`/fees/types/${id}`),
  getInvoices: (params?: Record<string, any>) => api.get('/fees/invoices', { params }),
  createInvoice: (data: Record<string, any>) => api.post('/fees/invoices', data),
  payInvoice: (invoiceId: number, data: Record<string, any>) => api.post(`/fees/invoices/${invoiceId}/pay`, data),
};

export const examScheduleAPI = {
  getList: (params?: Record<string, any>) => api.get('/exam-schedules', { params }),
  create: (data: Record<string, any>) => api.post('/exam-schedules', data),
  update: (id: number, data: Record<string, any>) => api.put(`/exam-schedules/${id}`, data),
  delete: (id: number) => api.delete(`/exam-schedules/${id}`),
};

export const assignmentAPI = {
  getList: (params?: Record<string, any>) => api.get('/assignments', { params }),
  getById: (id: number) => api.get(`/assignments/${id}`),
  create: (data: Record<string, any>) => api.post('/assignments', data),
  update: (id: number, data: Record<string, any>) => api.put(`/assignments/${id}`, data),
  delete: (id: number) => api.delete(`/assignments/${id}`),
  submit: (id: number, data: Record<string, any>) => api.post(`/assignments/${id}/submit`, data),
  grade: (id: number, submissionId: number, data: Record<string, any>) =>
    api.post(`/assignments/${id}/submissions/${submissionId}/grade`, data),
};

export const messageAPI = {
  getConversations: () => api.get('/messages/conversations'),
  createConversation: (data: { participant_ids: number[]; subject?: string }) =>
    api.post('/messages/conversations', data),
  getMessages: (conversationId: number) =>
    api.get(`/messages/conversations/${conversationId}`),
  send: (data: { conversation_id: number; body: string }) =>
    api.post('/messages/send', data),
};

export const uploadAPI = {
  uploadPhoto: (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload/photo', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
  },
  uploadDocument: (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload/document', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
  },
};

export const reportAPI = {
  studentReportCard: (studentId: number) =>
    api.get(`/reports/student-report-card/${studentId}`),
  attendanceReport: (params?: Record<string, any>) =>
    api.get('/reports/attendance', { params }),
  transcript: (studentId: number) =>
    api.get(`/reports/transcript/${studentId}`),
};

export const notificationAPI = {
  getList: (params?: Record<string, any>) =>
    api.get('/notifications', { params }),
  getUnread: () => api.get('/notifications/unread'),
  markAsRead: (id: string) => api.post(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/mark-all-read'),
};

export const academicYearAPI = {
  getList: (params?: Record<string, any>) =>
    api.get('/academic-years', { params }),

  getPaginated: (params?: Record<string, any>) =>
    api.get('/academic-years/paginated', { params }),

  getActive: () => api.get('/academic-years/active'),

  getById: (id: number) => api.get(`/academic-years/${id}`),

  create: (data: Record<string, any>) =>
    api.post('/academic-years', data),

  update: (id: number, data: Record<string, any>) =>
    api.put(`/academic-years/${id}`, data),

  delete: (id: number) => api.delete(`/academic-years/${id}`),

  getTerms: (academicYearId: number) =>
    api.get(`/academic-years/${academicYearId}/terms`),

  createTerm: (academicYearId: number, data: Record<string, any>) =>
    api.post(`/academic-years/${academicYearId}/terms`, data),

  updateTerm: (id: number, data: Record<string, any>) =>
    api.put(`/academic-years/terms/${id}`, data),

  deleteTerm: (id: number) => api.delete(`/academic-years/terms/${id}`),
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
