import api from './client';

export const announcementAPI = {
  getAll: (params?: Record<string, any>) => api.get('/announcements', { params }),
  create: (data: Record<string, any>) => api.post('/announcements', data),
  update: (id: number, data: Record<string, any>) => api.put(`/announcements/${id}`, data),
  delete: (id: number) => api.delete(`/announcements/${id}`),
};

export const messageAPI = {
  getConversations: () => api.get('/messages/conversations'),
  createConversation: (data: { participant_ids: number[]; subject?: string }) =>
    api.post('/messages/conversations', data),
  getMessages: (conversationId: number) => api.get(`/messages/conversations/${conversationId}`),
  send: (data: { conversation_id: number; body: string }) => api.post('/messages/send', data),
};

export const notificationAPI = {
  getList: (params?: Record<string, any>) => api.get('/notifications', { params }),
  getUnread: () => api.get('/notifications/unread'),
  markAsRead: (id: string) => api.post(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/mark-all-read'),
};
