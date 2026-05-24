import api from './client';

export const eventAPI = {
  getAll: (params?: Record<string, any>) => api.get('/events', { params }),
  create: (data: Record<string, any>) => api.post('/events', data),
  update: (id: number, data: Record<string, any>) => api.put(`/events/${id}`, data),
  delete: (id: number) => api.delete(`/events/${id}`),
};
