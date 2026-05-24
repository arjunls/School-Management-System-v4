import api from './client';

export const feeAPI = {
  getTypes: () => api.get('/fees/types'),
  createType: (data: Record<string, any>) => api.post('/fees/types', data),
  updateType: (id: number, data: Record<string, any>) => api.put(`/fees/types/${id}`, data),
  deleteType: (id: number) => api.delete(`/fees/types/${id}`),
  getInvoices: (params?: Record<string, any>) => api.get('/fees/invoices', { params }),
  createInvoice: (data: Record<string, any>) => api.post('/fees/invoices', data),
  payInvoice: (invoiceId: number, data: Record<string, any>) => api.post(`/fees/invoices/${invoiceId}/pay`, data),
};
