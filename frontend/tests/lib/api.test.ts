import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'

vi.mock('@/lib/api')

import api, {
  authAPI, studentAPI, teacherAPI, classAPI,
  gradeAPI, subjectAPI, scheduleAPI, attendanceAPI, libraryAPI,
  healthAPI, eventAPI, announcementAPI, quizAPI, extracurricularAPI,
  feeAPI, examScheduleAPI, assignmentAPI, messageAPI, notificationAPI,
  academicYearAPI, uploadAPI, reportAPI,
  mockGet, mockPost, mockPut, mockDelete,
} from '@/lib/api'

describe('API modules', () => {
  beforeEach(() => { vi.clearAllMocks() })

  it('authAPI.login calls correct endpoint', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await authAPI.login('test@test.com', 'password')
    expect(mockPost).toHaveBeenCalledWith('/auth/login', { email: 'test@test.com', password: 'password' })
  })

  it('authAPI.getProfile calls correct endpoint', async () => {
    mockGet.mockResolvedValueOnce({ data: { success: true } })
    await authAPI.getProfile()
    expect(mockGet).toHaveBeenCalledWith('/auth/profile')
  })

  it('studentAPI.getList sends params', async () => {
    mockGet.mockResolvedValueOnce({ data: [] })
    await studentAPI.getList({ page: 1 })
    expect(mockGet).toHaveBeenCalledWith('/students', { params: { page: 1 } })
  })

  it('studentAPI.create sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await studentAPI.create({ name: 'John' })
    expect(mockPost).toHaveBeenCalledWith('/students', { name: 'John' })
  })

  it('studentAPI.delete sends DELETE', async () => {
    mockDelete.mockResolvedValueOnce({ data: { success: true } })
    await studentAPI.delete('5')
    expect(mockDelete).toHaveBeenCalledWith('/students/5')
  })

  it('teacherAPI.getPaginated sends params', async () => {
    mockGet.mockResolvedValueOnce({ data: [] })
    await teacherAPI.getPaginated({ page: 1 })
    expect(mockGet).toHaveBeenCalledWith('/teachers/paginated', { params: { page: 1 } })
  })

  it('classAPI.create sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await classAPI.create({ name: 'X A' })
    expect(mockPost).toHaveBeenCalledWith('/classes', { name: 'X A' })
  })

  it('libraryAPI.createLoan sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await libraryAPI.createLoan({ book_id: 1, user_id: 1, due_date: '2026-06-01' })
    expect(mockPost).toHaveBeenCalledWith('/library/loans', { book_id: 1, user_id: 1, due_date: '2026-06-01' })
  })

  it('libraryAPI.returnBook sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await libraryAPI.returnBook(1)
    expect(mockPost).toHaveBeenCalledWith('/library/loans/1/return')
  })

  it('healthAPI.save sends PUT', async () => {
    mockPut.mockResolvedValueOnce({ data: { success: true } })
    await healthAPI.save(1, { blood_type: 'O+' })
    expect(mockPut).toHaveBeenCalledWith('/health/1', { blood_type: 'O+' })
  })

  it('quizAPI.create sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await quizAPI.create({ title: 'Quiz', class_id: 1, subject_id: 1, status: 'draft' })
    expect(mockPost).toHaveBeenCalledWith('/quizzes', { title: 'Quiz', class_id: 1, subject_id: 1, status: 'draft' })
  })

  it('extracurricularAPI.join sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await extracurricularAPI.join(1)
    expect(mockPost).toHaveBeenCalledWith('/extracurriculars/1/join')
  })

  it('assignmentAPI.grade sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await assignmentAPI.grade(1, 1, { score: 85 })
    expect(mockPost).toHaveBeenCalledWith('/assignments/1/submissions/1/grade', { score: 85 })
  })

  it('uploadAPI.uploadPhoto sends FormData', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    const file = new File([''], 'photo.jpg', { type: 'image/jpeg' })
    await uploadAPI.uploadPhoto(file)
    expect(mockPost).toHaveBeenCalledWith('/upload/photo', expect.any(FormData), expect.objectContaining({ headers: { 'Content-Type': 'multipart/form-data' } }))
  })

  it('reportAPI.studentReportCard sends GET', async () => {
    mockGet.mockResolvedValueOnce({ data: {} })
    await reportAPI.studentReportCard(1)
    expect(mockGet).toHaveBeenCalledWith('/reports/student-report-card/1')
  })

  it('authAPI.register sends POST', async () => {
    mockPost.mockResolvedValueOnce({ data: { success: true } })
    await authAPI.register({ name: 'Test', email: 't@t.com', password: '12345678', password_confirmation: '12345678' })
    expect(mockPost).toHaveBeenCalledWith('/auth/register', { name: 'Test', email: 't@t.com', password: '12345678', password_confirmation: '12345678' })
  })
})
