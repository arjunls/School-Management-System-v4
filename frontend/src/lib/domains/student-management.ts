import api from './client';

export const studentAPI = {
  getList: (params: Record<string, any>) => api.get('/students', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/students/paginated', { params }),
  getById: (id: string) => api.get(`/students/${id}`),
  create: (data: Record<string, any>) => api.post('/students', data),
  update: (id: string, data: Record<string, any>) => api.put(`/students/${id}`, data),
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

export const attendanceAPI = {
  getList: (params: Record<string, any>) => api.get('/attendance', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/attendance/paginated', { params }),
  create: (data: Record<string, any>) => api.post('/attendance', data),
  update: (id: string, data: Record<string, any>) => api.put(`/attendance/${id}`, data),
  delete: (id: string) => api.delete(`/attendance/${id}`),
};

export const healthAPI = {
  get: (studentId: number) => api.get(`/health/${studentId}`),
  save: (studentId: number, data: Record<string, any>) => api.put(`/health/${studentId}`, data),
};
