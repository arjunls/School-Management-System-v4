import api from './client';

export const teacherAPI = {
  getList: (params: Record<string, any>) => api.get('/teachers', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/teachers/paginated', { params }),
  getById: (id: string) => api.get(`/teachers/${id}`),
  create: (data: Record<string, any>) => api.post('/teachers', data),
  update: (id: string, data: Record<string, any>) => api.put(`/teachers/${id}`, data),
  delete: (id: string) => api.delete(`/teachers/${id}`),
};
