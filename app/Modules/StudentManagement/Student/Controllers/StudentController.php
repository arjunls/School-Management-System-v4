<?php

namespace App\Modules\StudentManagement\Student\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentManagement\Student\Requests\GetStudentByEmailRequest;
use App\Modules\StudentManagement\Student\Requests\StoreStudentRequest;
use App\Modules\StudentManagement\Student\Requests\UpdateStudentRequest;
use App\Modules\StudentManagement\Student\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @group Students
 *
 * APIs for managing students
 */
class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Get a student by ID
     */
    public function getStudent($id)
    {
        try {
            $student = $this->studentService->getStudent($id);
            if (!$student) {
                return $this->notFound('Student not found');
            }

            return $this->success($student);
        } catch (\Exception $e) {
            Log::error('Error fetching student', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get a student by email address
     */
    public function getStudentByEmail(GetStudentByEmailRequest $request)
    {
        try {
            $student = $this->studentService->getStudentByEmail($request->email);
            if (!$student) {
                return $this->notFound('Student not found');
            }

            return $this->success($student);
        } catch (\Exception $e) {
            Log::error('Error fetching student by email', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new student
     */
    public function createStudent(StoreStudentRequest $request)
    {
        try {
            $student = $this->studentService->createStudent($request->validated());

            return $this->created($student, 'Student created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating student', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing student
     */
    public function updateStudent(UpdateStudentRequest $request, $id)
    {
        try {
            $student = $this->studentService->updateStudent($id, $request->validated());
            if (!$student) {
                return $this->notFound('Student not found');
            }

            return $this->success($student, 'Student updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating student', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a student
     */
    public function deleteStudent($id)
    {
        try {
            $result = $this->studentService->deleteStudent($id);
            if (!$result) {
                return $this->notFound('Student not found');
            }

            return $this->deleted('Student deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting student', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get all students with optional filters
     */
    public function getAllStudents(Request $request)
    {
        try {
            $filters = $request->only(['name', 'email', 'status', 'kelas_id', 'nisn']);
            $students = $this->studentService->getAllStudents($filters);

            return $this->success($students);
        } catch (\Exception $e) {
            Log::error('Error fetching students', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of students
     */
    public function getStudentsPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $students = $this->studentService->getStudentsPaginated($perPage, $filters);

            return $this->paginated($students);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated students', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }
}
