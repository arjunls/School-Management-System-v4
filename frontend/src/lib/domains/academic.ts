import api from './client';

export const academicYearAPI = {
  getList: (params?: Record<string, any>) => api.get('/academic-years', { params }),
  getPaginated: (params?: Record<string, any>) => api.get('/academic-years/paginated', { params }),
  getActive: () => api.get('/academic-years/active'),
  getById: (id: number) => api.get(`/academic-years/${id}`),
  create: (data: Record<string, any>) => api.post('/academic-years', data),
  update: (id: number, data: Record<string, any>) => api.put(`/academic-years/${id}`, data),
  delete: (id: number) => api.delete(`/academic-years/${id}`),
  getTerms: (academicYearId: number) => api.get(`/academic-years/${academicYearId}/terms`),
  createTerm: (academicYearId: number, data: Record<string, any>) => api.post(`/academic-years/${academicYearId}/terms`, data),
  updateTerm: (id: number, data: Record<string, any>) => api.put(`/academic-years/terms/${id}`, data),
  deleteTerm: (id: number) => api.delete(`/academic-years/terms/${id}`),
};

export const classAPI = {
  getList: (params?: Record<string, any>) => api.get('/classes', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/classes/paginated', { params }),
  getById: (id: string) => api.get(`/classes/${id}`),
  create: (data: Record<string, any>) => api.post('/classes', data),
  update: (id: string, data: Record<string, any>) => api.put(`/classes/${id}`, data),
  delete: (id: string) => api.delete(`/classes/${id}`),
  getStudents: (classId: string) => api.get(`/classes/${classId}/students`),
  addStudent: (classId: string, studentId: string) => api.post(`/classes/${classId}/students/${studentId}`),
  removeStudent: (classId: string, studentId: string) => api.delete(`/classes/${classId}/students/${studentId}`),
};

export const subjectAPI = {
  getList: (params?: Record<string, any>) => api.get('/subjects', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/subjects/paginated', { params }),
  getById: (id: string) => api.get(`/subjects/${id}`),
  create: (data: Record<string, any>) => api.post('/subjects', data),
  update: (id: string, data: Record<string, any>) => api.put(`/subjects/${id}`, data),
  delete: (id: string) => api.delete(`/subjects/${id}`),
};

export const scheduleAPI = {
  getList: (params?: Record<string, any>) => api.get('/schedules', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/schedules/paginated', { params }),
  getById: (id: string) => api.get(`/schedules/${id}`),
  create: (data: Record<string, any>) => api.post('/schedules', data),
  update: (id: string, data: Record<string, any>) => api.put(`/schedules/${id}`, data),
  delete: (id: string) => api.delete(`/schedules/${id}`),
};
