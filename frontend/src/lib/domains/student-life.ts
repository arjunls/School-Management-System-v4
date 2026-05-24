import api from './client';

export const extracurricularAPI = {
  getAll: () => api.get('/extracurriculars'),
  create: (data: Record<string, any>) => api.post('/extracurriculars', data),
  update: (id: number, data: Record<string, any>) => api.put(`/extracurriculars/${id}`, data),
  delete: (id: number) => api.delete(`/extracurriculars/${id}`),
  join: (id: number) => api.post(`/extracurriculars/${id}/join`),
  leave: (id: number) => api.post(`/extracurriculars/${id}/leave`),
};
