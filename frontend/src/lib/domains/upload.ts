import api from './client';

export const uploadAPI = {
  uploadPhoto: (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload/photo', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
  },
  uploadDocument: (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload/document', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
  },
};
