import api from './client';

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
