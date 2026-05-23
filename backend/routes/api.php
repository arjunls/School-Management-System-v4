<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Teacher\Controllers\TeacherController;
use App\Modules\Class\Controllers\ClassController;
use App\Modules\Subject\Controllers\SubjectController;
use App\Modules\Schedule\Controllers\ScheduleController;
use App\Modules\Grade\Controllers\GradeController;
use App\Modules\Attendance\Controllers\AttendanceController;
use App\Modules\AcademicYear\Controllers\AcademicYearController;
use App\Modules\Parent\Controllers\ParentController;
use App\Modules\Notification\Controllers\NotificationController;
use App\Modules\Report\Controllers\ReportController;
use App\Modules\Upload\Controllers\UploadController;
use App\Modules\Message\Controllers\MessageController;
use App\Modules\Assignment\Controllers\AssignmentController;
use App\Modules\ExamSchedule\Controllers\ExamScheduleController;
use App\Modules\Announcement\Controllers\AnnouncementController;
use App\Modules\Calendar\Controllers\EventController;
use App\Modules\Extracurricular\Controllers\ExtracurricularController;
use App\Modules\Health\Controllers\HealthController;
use App\Modules\Fee\Controllers\FeeController;
use App\Modules\Quiz\Controllers\QuizController;
use App\Modules\Library\Controllers\LibraryController;
use App\Modules\User\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes (hybrid API auth using Sanctum tokens)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/me', [AuthController::class, 'profile']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/attendance-chart', [DashboardController::class, 'getAttendanceChartData']);
    Route::get('/performance-chart', [DashboardController::class, 'getPerformanceChartData']);
    Route::get('/student-performance/{studentId}', [DashboardController::class, 'getStudentPerformanceTrend']);
});

// User Management (admin only)
Route::prefix('users')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [UserController::class, 'getAllUsers']);
    Route::get('/paginated', [UserController::class, 'getUsersPaginated']);
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
    Route::get('/by-email', [UserController::class, 'getUserByEmail']);
});

// Student Routes
Route::prefix('students')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [StudentController::class, 'getAllStudents']);
    Route::get('/paginated', [StudentController::class, 'getStudentsPaginated']);
    Route::get('/{id}', [StudentController::class, 'getStudent']);
    Route::get('/by-email', [StudentController::class, 'getStudentByEmail']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [StudentController::class, 'createStudent']);
        Route::put('/{id}', [StudentController::class, 'updateStudent']);
        Route::delete('/{id}', [StudentController::class, 'deleteStudent']);
    });
});

// Teacher Routes
Route::prefix('teachers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TeacherController::class, 'getAllTeachers']);
    Route::get('/paginated', [TeacherController::class, 'getTeachersPaginated']);
    Route::get('/{id}', [TeacherController::class, 'getTeacher']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [TeacherController::class, 'createTeacher']);
        Route::put('/{id}', [TeacherController::class, 'updateTeacher']);
        Route::delete('/{id}', [TeacherController::class, 'deleteTeacher']);
    });
});

// Class Routes
Route::prefix('classes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ClassController::class, 'getAllClasses']);
    Route::get('/paginated', [ClassController::class, 'getClassesPaginated']);
    Route::get('/{id}', [ClassController::class, 'getClass']);
    Route::get("/{classId}/students", [ClassController::class, "getClassStudents"]);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [ClassController::class, 'createClass']);
        Route::put('/{id}', [ClassController::class, 'updateClass']);
        Route::delete('/{id}', [ClassController::class, 'deleteClass']);
        Route::post("/{classId}/students/{studentId}", [ClassController::class, "addStudentToClass"]);
        Route::delete("/{classId}/students/{studentId}", [ClassController::class, "removeStudentFromClass"]);
    });
});

// Subject Routes
Route::prefix('subjects')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SubjectController::class, 'getAllSubjects']);
    Route::get('/paginated', [SubjectController::class, 'getSubjectsPaginated']);
    Route::get('/{id}', [SubjectController::class, 'getSubject']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [SubjectController::class, 'createSubject']);
        Route::put('/{id}', [SubjectController::class, 'updateSubject']);
        Route::delete('/{id}', [SubjectController::class, 'deleteSubject']);
    });
});

// Schedule Routes
Route::prefix('schedules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ScheduleController::class, 'getAllSchedules']);
    Route::get('/paginated', [ScheduleController::class, 'getSchedulesPaginated']);
    Route::get('/{id}', [ScheduleController::class, 'getSchedule']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [ScheduleController::class, 'createSchedule']);
        Route::put('/{id}', [ScheduleController::class, 'updateSchedule']);
        Route::delete('/{id}', [ScheduleController::class, 'deleteSchedule']);
    });
});

// Grade Routes
Route::prefix('grades')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [GradeController::class, 'getAllGrades']);
    Route::get('/paginated', [GradeController::class, 'getGradesPaginated']);
    Route::get('/{id}', [GradeController::class, 'getGrade']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [GradeController::class, 'createGrade']);
        Route::put('/{id}', [GradeController::class, 'updateGrade']);
        Route::delete('/{id}', [GradeController::class, 'deleteGrade']);
    });
});

// Attendance Routes
Route::prefix('attendance')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AttendanceController::class, 'getAllAttendance']);
    Route::get('/paginated', [AttendanceController::class, 'getAttendancePaginated']);
    Route::get('/{id}', [AttendanceController::class, 'getAttendance']);

    // Write operations restricted to admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [AttendanceController::class, 'createAttendance']);
        Route::put('/{id}', [AttendanceController::class, 'updateAttendance']);
        Route::delete('/{id}', [AttendanceController::class, 'deleteAttendance']);
    });
});

// Export Routes (CSV downloads)
Route::prefix('export')->middleware('auth:sanctum')->group(function () {
    Route::get('/students', [\App\Modules\Export\Controllers\ExportController::class, 'studentsCSV']);
    Route::get('/teachers', [\App\Modules\Export\Controllers\ExportController::class, 'teachersCSV']);
    Route::get('/classes', [\App\Modules\Export\Controllers\ExportController::class, 'classesCSV']);
    Route::get('/subjects', [\App\Modules\Export\Controllers\ExportController::class, 'subjectsCSV']);
    Route::get('/grades', [\App\Modules\Export\Controllers\ExportController::class, 'gradesCSV']);
    Route::get('/attendance', [\App\Modules\Export\Controllers\ExportController::class, 'attendanceCSV']);
});

// Import Routes (CSV upload)
Route::prefix('import')->middleware('auth:sanctum')->group(function () {
    Route::post('/students', [\App\Modules\Import\Controllers\ImportController::class, 'importStudents']);
    Route::post('/teachers', [\App\Modules\Import\Controllers\ImportController::class, 'importTeachers']);
});

// Academic Years & Terms (admin only)
Route::prefix('academic-years')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [AcademicYearController::class, 'getAll']);
    Route::get('/paginated', [AcademicYearController::class, 'paginate']);
    Route::get('/active', [AcademicYearController::class, 'getActive']);
    Route::get('/{id}', [AcademicYearController::class, 'find']);
    Route::post('/', [AcademicYearController::class, 'create']);
    Route::put('/{id}', [AcademicYearController::class, 'update']);
    Route::delete('/{id}', [AcademicYearController::class, 'delete']);

    // Terms nested under academic years
    Route::get('/{academicYearId}/terms', [AcademicYearController::class, 'getTerms']);
    Route::post('/{academicYearId}/terms', [AcademicYearController::class, 'createTerm']);
    Route::put('/terms/{id}', [AcademicYearController::class, 'updateTerm']);
    Route::delete('/terms/{id}', [AcademicYearController::class, 'deleteTerm']);
});

// Parent routes
Route::prefix('parents')->middleware('auth:sanctum')->group(function () {
    Route::get('/children', [ParentController::class, 'getChildren']);
    Route::post('/link', [ParentController::class, 'linkParentToStudent'])->middleware('role:admin');
    Route::post('/unlink', [ParentController::class, 'unlinkParentFromStudent'])->middleware('role:admin');
    Route::get('/students/{studentId}/parents', [ParentController::class, 'getStudentParents']);
    Route::get('/students/{studentId}/grades', [ParentController::class, 'getStudentGrade']);
});

// Notification routes
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

// Report routes
Route::prefix('reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/student-report-card/{studentId}', [ReportController::class, 'studentReportCard']);
    Route::get('/attendance', [ReportController::class, 'attendanceReport']);
    Route::get('/transcript/{studentId}', [ReportController::class, 'transcript']);
});

// Upload routes
Route::prefix('upload')->middleware('auth:sanctum')->group(function () {
    Route::post('/photo', [UploadController::class, 'uploadPhoto']);
    Route::post('/document', [UploadController::class, 'uploadDocument']);
});

// Message routes
Route::prefix('messages')->middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/{conversationId}', [MessageController::class, 'messages']);
    Route::post('/send', [MessageController::class, 'send']);
});

// Assignment routes
Route::prefix('assignments')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::post('/', [AssignmentController::class, 'store'])->middleware('role:admin,teacher');
    Route::get('/{id}', [AssignmentController::class, 'show']);
    Route::put('/{id}', [AssignmentController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [AssignmentController::class, 'destroy'])->middleware('role:admin,teacher');
    Route::post('/{id}/submit', [AssignmentController::class, 'submit'])->middleware('role:student');
    Route::post('/{id}/submissions/{submissionId}/grade', [AssignmentController::class, 'grade'])->middleware('role:admin,teacher');
});

// Exam Schedule routes
Route::prefix('exam-schedules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ExamScheduleController::class, 'index']);
    Route::post('/', [ExamScheduleController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [ExamScheduleController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [ExamScheduleController::class, 'destroy'])->middleware('role:admin,teacher');
});

// Library routes
Route::prefix('library')->middleware('auth:sanctum')->group(function () {
    Route::get('/books', [LibraryController::class, 'books']);
    Route::post('/books', [LibraryController::class, 'bookStore'])->middleware('role:admin,teacher');
    Route::put('/books/{id}', [LibraryController::class, 'bookUpdate'])->middleware('role:admin,teacher');
    Route::delete('/books/{id}', [LibraryController::class, 'bookDelete'])->middleware('role:admin,teacher');
    Route::get('/loans', [LibraryController::class, 'loans']);
    Route::post('/loans', [LibraryController::class, 'loanStore'])->middleware('role:admin,teacher');
    Route::post('/loans/{id}/return', [LibraryController::class, 'loanReturn'])->middleware('role:admin,teacher');
});

// Fee routes
Route::prefix('fees')->middleware('auth:sanctum')->group(function () {
    Route::get('/types', [FeeController::class, 'types']);
    Route::post('/types', [FeeController::class, 'typeStore'])->middleware('role:admin');
    Route::put('/types/{id}', [FeeController::class, 'typeUpdate'])->middleware('role:admin');
    Route::delete('/types/{id}', [FeeController::class, 'typeDelete'])->middleware('role:admin');
    Route::get('/invoices', [FeeController::class, 'invoices']);
    Route::post('/invoices', [FeeController::class, 'invoiceStore'])->middleware('role:admin');
    Route::post('/invoices/{invoiceId}/pay', [FeeController::class, 'pay'])->middleware('role:admin,student');
});

// Extracurricular routes
Route::prefix('extracurriculars')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ExtracurricularController::class, 'index']);
    Route::post('/', [ExtracurricularController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [ExtracurricularController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [ExtracurricularController::class, 'destroy'])->middleware('role:admin');
    Route::post('/{id}/join', [ExtracurricularController::class, 'join'])->middleware('role:student');
    Route::post('/{id}/leave', [ExtracurricularController::class, 'leave'])->middleware('role:student');
});

// Quiz routes
Route::prefix('quizzes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [QuizController::class, 'index']);
    Route::post('/', [QuizController::class, 'store'])->middleware('role:admin,teacher');
    Route::get('/{id}', [QuizController::class, 'show']);
    Route::put('/{id}', [QuizController::class, 'update'])->middleware('role:teacher');
    Route::delete('/{id}', [QuizController::class, 'destroy'])->middleware('role:admin,teacher');

    Route::post('/{quizId}/questions', [QuizController::class, 'addQuestion'])->middleware('role:teacher');
    Route::put('/questions/{id}', [QuizController::class, 'updateQuestion'])->middleware('role:teacher');
    Route::delete('/questions/{id}', [QuizController::class, 'deleteQuestion'])->middleware('role:teacher');

    Route::post('/{quizId}/start', [QuizController::class, 'start'])->middleware('role:student');
    Route::post('/attempts/{attemptId}/submit', [QuizController::class, 'submit'])->middleware('role:student');
    Route::get('/attempts', [QuizController::class, 'attemptsList']);
    Route::post('/attempts/{attemptId}/grade/{questionId}', [QuizController::class, 'gradeEssay'])->middleware('role:teacher');
});

// Announcement routes
Route::prefix('announcements')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::post('/', [AnnouncementController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [AnnouncementController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->middleware('role:admin');
});

// Calendar / Event routes
Route::prefix('events')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [EventController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [EventController::class, 'destroy'])->middleware('role:admin');
});

// Health Record routes
Route::prefix('health')->middleware('auth:sanctum')->group(function () {
    Route::get('/{studentId}', [HealthController::class, 'show']);
    Route::put('/{studentId}', [HealthController::class, 'upsert'])->middleware('role:admin,teacher');
});

// Audit Log routes (admin only)
Route::prefix('audit-logs')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [\App\Modules\Audit\Controllers\AuditLogController::class, 'index']);
    Route::get('/{id}', [\App\Modules\Audit\Controllers\AuditLogController::class, 'show']);
});
