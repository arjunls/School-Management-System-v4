// Re-export from modular domain structure
// Import from specific domains for better tree-shaking:
//   import { authAPI } from '@/lib/domains/auth';
//   import { studentAPI } from '@/lib/domains/student-management';
export { default } from './domains/client';
export * from './domains';
