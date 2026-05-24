import api from './client';

export const gradeAPI = {
  getList: (params: Record<string, any>) => api.get('/grades', { params }),
  getPaginated: (params: Record<string, any>) => api.get('/grades/paginated', { params }),
  create: (data: Record<string, any>) => api.post('/grades', data),
  update: (id: string, data: Record<string, any>) => api.put(`/grades/${id}`, data),
  delete: (id: string) => api.delete(`/grades/${id}`),
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
  gradeEssay: (attemptId: number, questionId: number, data: Record<string, any>) =>
    api.post(`/quizzes/attempts/${attemptId}/grade/${questionId}`, data),
};

export const examScheduleAPI = {
  getList: (params?: Record<string, any>) => api.get('/exam-schedules', { params }),
  create: (data: Record<string, any>) => api.post('/exam-schedules', data),
  update: (id: number, data: Record<string, any>) => api.put(`/exam-schedules/${id}`, data),
  delete: (id: number) => api.delete(`/exam-schedules/${id}`),
};
