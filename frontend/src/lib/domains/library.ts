import api from './client';

export const libraryAPI = {
  getBooks: (params?: Record<string, any>) => api.get('/library/books', { params }),
  createBook: (data: Record<string, any>) => api.post('/library/books', data),
  updateBook: (id: number, data: Record<string, any>) => api.put(`/library/books/${id}`, data),
  deleteBook: (id: number) => api.delete(`/library/books/${id}`),
  getLoans: (params?: Record<string, any>) => api.get('/library/loans', { params }),
  createLoan: (data: Record<string, any>) => api.post('/library/loans', data),
  returnBook: (id: number) => api.post(`/library/loans/${id}/return`),
};
