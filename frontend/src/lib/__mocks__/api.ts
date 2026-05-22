const mockGet = vi.fn()
const mockPost = vi.fn()
const mockPut = vi.fn()
const mockDelete = vi.fn()

const api = {
  get: mockGet,
  post: mockPost,
  put: mockPut,
  delete: mockDelete,
  interceptors: { request: { use: vi.fn() }, response: { use: vi.fn() } },
  defaults: { headers: { common: {} } },
} as any

export default api

export const authAPI = {
  login: (email: string, password: string) => api.post('/auth/login', { email, password }),
  register: (data: any) => api.post('/auth/register', data),
  logout: () => api.post('/auth/logout'),
  getProfile: () => api.get('/auth/profile'),
  refresh: () => api.post('/auth/refresh'),
  changePassword: (data: any) => api.post('/auth/change-password', data),
  forgotPassword: (email: string) => api.post('/auth/forgot-password', { email }),
  resetPassword: (data: any) => api.post('/auth/reset-password', data),
}

export const dashboardAPI = {
  getStats: (params?: any) => api.get('/dashboard/stats', { params }),
  getAttendanceChartData: (params?: any) => api.get('/dashboard/attendance-chart', { params }),
  getPerformanceChartData: (params?: any) => api.get('/dashboard/performance-chart', { params }),
  getStudentPerformanceTrend: (studentId: number, params?: any) => api.get(`/dashboard/student-performance/${studentId}`, { params }),
}

export const studentAPI = {
  getList: (params: any) => api.get('/students', { params }),
  getPaginated: (params: any) => api.get('/students/paginated', { params }),
  getById: (id: string) => api.get(`/students/${id}`),
  create: (data: any) => api.post('/students', data),
  update: (id: string, data: any) => api.put(`/students/${id}`, data),
  delete: (id: string) => api.delete(`/students/${id}`),
}

export const parentAPI = {
  getChildren: () => api.get('/parents/children'),
  link: (data: any) => api.post('/parents/link', data),
  unlink: (data: any) => api.post('/parents/unlink', data),
  getStudentParents: (studentId: number) => api.get(`/parents/students/${studentId}/parents`),
  getStudentGrades: (studentId: number) => api.get(`/parents/students/${studentId}/grades`),
}

export const teacherAPI = {
  getList: (params: any) => api.get('/teachers', { params }),
  getPaginated: (params: any) => api.get('/teachers/paginated', { params }),
  getById: (id: string) => api.get(`/teachers/${id}`),
  create: (data: any) => api.post('/teachers', data),
  update: (id: string, data: any) => api.put(`/teachers/${id}`, data),
  delete: (id: string) => api.delete(`/teachers/${id}`),
}

export const classAPI = {
  getList: (params?: any) => api.get('/classes', { params }),
  getPaginated: (params: any) => api.get('/classes/paginated', { params }),
  getById: (id: string) => api.get(`/classes/${id}`),
  create: (data: any) => api.post('/classes', data),
  update: (id: string, data: any) => api.put(`/classes/${id}`, data),
  delete: (id: string) => api.delete(`/classes/${id}`),
}

export const gradeAPI = {
  getList: (params: any) => api.get('/grades', { params }),
  getPaginated: (params: any) => api.get('/grades/paginated', { params }),
  create: (data: any) => api.post('/grades', data),
  update: (id: string, data: any) => api.put(`/grades/${id}`, data),
  delete: (id: string) => api.delete(`/grades/${id}`),
}

export const subjectAPI = {
  getList: (params?: any) => api.get('/subjects', { params }),
  getPaginated: (params: any) => api.get('/subjects/paginated', { params }),
  getById: (id: string) => api.get(`/subjects/${id}`),
  create: (data: any) => api.post('/subjects', data),
  update: (id: string, data: any) => api.put(`/subjects/${id}`, data),
  delete: (id: string) => api.delete(`/subjects/${id}`),
}

export const scheduleAPI = {
  getList: (params?: any) => api.get('/schedules', { params }),
  getPaginated: (params: any) => api.get('/schedules/paginated', { params }),
  getById: (id: string) => api.get(`/schedules/${id}`),
  create: (data: any) => api.post('/schedules', data),
  update: (id: string, data: any) => api.put(`/schedules/${id}`, data),
  delete: (id: string) => api.delete(`/schedules/${id}`),
}

export const attendanceAPI = {
  getList: (params: any) => api.get('/attendance', { params }),
  getPaginated: (params: any) => api.get('/attendance/paginated', { params }),
  create: (data: any) => api.post('/attendance', data),
  update: (id: string, data: any) => api.put(`/attendance/${id}`, data),
  delete: (id: string) => api.delete(`/attendance/${id}`),
}

export const libraryAPI = {
  getBooks: (params?: any) => api.get('/library/books', { params }),
  createBook: (data: any) => api.post('/library/books', data),
  updateBook: (id: number, data: any) => api.put(`/library/books/${id}`, data),
  deleteBook: (id: number) => api.delete(`/library/books/${id}`),
  getLoans: (params?: any) => api.get('/library/loans', { params }),
  createLoan: (data: any) => api.post('/library/loans', data),
  returnBook: (id: number) => api.post(`/library/loans/${id}/return`),
}

export const healthAPI = {
  get: (studentId: number) => api.get(`/health/${studentId}`),
  save: (studentId: number, data: any) => api.put(`/health/${studentId}`, data),
}

export const eventAPI = {
  getAll: (params?: any) => api.get('/events', { params }),
  create: (data: any) => api.post('/events', data),
  update: (id: number, data: any) => api.put(`/events/${id}`, data),
  delete: (id: number) => api.delete(`/events/${id}`),
}

export const announcementAPI = {
  getAll: (params?: any) => api.get('/announcements', { params }),
  create: (data: any) => api.post('/announcements', data),
  update: (id: number, data: any) => api.put(`/announcements/${id}`, data),
  delete: (id: number) => api.delete(`/announcements/${id}`),
}

export const quizAPI = {
  getAll: (params?: any) => api.get('/quizzes', { params }),
  get: (id: number) => api.get(`/quizzes/${id}`),
  create: (data: any) => api.post('/quizzes', data),
  update: (id: number, data: any) => api.put(`/quizzes/${id}`, data),
  delete: (id: number) => api.delete(`/quizzes/${id}`),
  addQuestion: (quizId: number, data: any) => api.post(`/quizzes/${quizId}/questions`, data),
  updateQuestion: (id: number, data: any) => api.put(`/quizzes/questions/${id}`, data),
  deleteQuestion: (id: number) => api.delete(`/quizzes/questions/${id}`),
  start: (quizId: number) => api.post(`/quizzes/${quizId}/start`),
  submit: (attemptId: number, data: any) => api.post(`/quizzes/attempts/${attemptId}/submit`, data),
  attempts: (params?: any) => api.get('/quizzes/attempts', { params }),
  gradeEssay: (attemptId: number, questionId: number, data: any) => api.post(`/quizzes/attempts/${attemptId}/grade/${questionId}`, data),
}

export const extracurricularAPI = {
  getAll: () => api.get('/extracurriculars'),
  create: (data: any) => api.post('/extracurriculars', data),
  update: (id: number, data: any) => api.put(`/extracurriculars/${id}`, data),
  delete: (id: number) => api.delete(`/extracurriculars/${id}`),
  join: (id: number) => api.post(`/extracurriculars/${id}/join`),
  leave: (id: number) => api.post(`/extracurriculars/${id}/leave`),
}

export const feeAPI = {
  getTypes: () => api.get('/fees/types'),
  createType: (data: any) => api.post('/fees/types', data),
  updateType: (id: number, data: any) => api.put(`/fees/types/${id}`, data),
  deleteType: (id: number) => api.delete(`/fees/types/${id}`),
  getInvoices: (params?: any) => api.get('/fees/invoices', { params }),
  createInvoice: (data: any) => api.post('/fees/invoices', data),
  payInvoice: (invoiceId: number, data: any) => api.post(`/fees/invoices/${invoiceId}/pay`, data),
}

export const examScheduleAPI = {
  getList: (params?: any) => api.get('/exam-schedules', { params }),
  create: (data: any) => api.post('/exam-schedules', data),
  update: (id: number, data: any) => api.put(`/exam-schedules/${id}`, data),
  delete: (id: number) => api.delete(`/exam-schedules/${id}`),
}

export const assignmentAPI = {
  getList: (params?: any) => api.get('/assignments', { params }),
  getById: (id: number) => api.get(`/assignments/${id}`),
  create: (data: any) => api.post('/assignments', data),
  update: (id: number, data: any) => api.put(`/assignments/${id}`, data),
  delete: (id: number) => api.delete(`/assignments/${id}`),
  submit: (id: number, data: any) => api.post(`/assignments/${id}/submit`, data),
  grade: (id: number, submissionId: number, data: any) => api.post(`/assignments/${id}/submissions/${submissionId}/grade`, data),
}

export const messageAPI = {
  getConversations: () => api.get('/messages/conversations'),
  createConversation: (data: any) => api.post('/messages/conversations', data),
  getMessages: (conversationId: number) => api.get(`/messages/conversations/${conversationId}`),
  send: (data: any) => api.post('/messages/send', data),
}

export const uploadAPI = {
  uploadPhoto: (file: File) => {
    const fd = new FormData()
    fd.append('file', file)
    return api.post('/upload/photo', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
  },
  uploadDocument: (file: File) => {
    const fd = new FormData()
    fd.append('file', file)
    return api.post('/upload/document', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
  },
}

export const reportAPI = {
  studentReportCard: (studentId: number) => api.get(`/reports/student-report-card/${studentId}`),
  attendanceReport: (params?: any) => api.get('/reports/attendance', { params }),
  transcript: (studentId: number) => api.get(`/reports/transcript/${studentId}`),
}

export const notificationAPI = {
  getList: (params?: any) => api.get('/notifications', { params }),
  getUnread: () => api.get('/notifications/unread'),
  markAsRead: (id: string) => api.post(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/mark-all-read'),
}

export const academicYearAPI = {
  getList: (params?: any) => api.get('/academic-years', { params }),
  getPaginated: (params?: any) => api.get('/academic-years/paginated', { params }),
  getActive: () => api.get('/academic-years/active'),
  getById: (id: number) => api.get(`/academic-years/${id}`),
  create: (data: any) => api.post('/academic-years', data),
  update: (id: number, data: any) => api.put(`/academic-years/${id}`, data),
  delete: (id: number) => api.delete(`/academic-years/${id}`),
  getTerms: (academicYearId: number) => api.get(`/academic-years/${academicYearId}/terms`),
  createTerm: (academicYearId: number, data: any) => api.post(`/academic-years/${academicYearId}/terms`, data),
  updateTerm: (id: number, data: any) => api.put(`/academic-years/terms/${id}`, data),
  deleteTerm: (id: number) => api.delete(`/academic-years/terms/${id}`),
}

export const exportAPI = {
  download: async (type: string) => {},
}

export { mockGet, mockPost, mockPut, mockDelete }
