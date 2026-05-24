<?php

namespace App\Modules\StaffManagement\Teacher\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\StaffManagement\Teacher\Requests\GetTeacherByEmailRequest;
use App\Modules\StaffManagement\Teacher\Requests\StoreTeacherRequest;
use App\Modules\StaffManagement\Teacher\Requests\UpdateTeacherRequest;
use App\Modules\StaffManagement\Teacher\Services\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @group Teachers
 *
 * APIs for managing teachers
 */
class TeacherController extends Controller
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Get a teacher by ID
     */
    public function getTeacher($id)
    {
        try {
            $teacher = $this->teacherService->getTeacher($id);
            if (!$teacher) {
                return $this->notFound('Teacher not found');
            }

            return $this->success($teacher);
        } catch (\Exception $e) {
            Log::error('Error fetching teacher', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get a teacher by email address
     */
    public function getTeacherByEmail(GetTeacherByEmailRequest $request)
    {
        try {
            $teacher = $this->teacherService->getTeacherByEmail($request->email);
            if (!$teacher) {
                return $this->notFound('Teacher not found');
            }

            return $this->success($teacher);
        } catch (\Exception $e) {
            Log::error('Error fetching teacher by email', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new teacher
     */
    public function createTeacher(StoreTeacherRequest $request)
    {
        try {
            $teacher = $this->teacherService->createTeacher($request->validated());

            return $this->created($teacher, 'Teacher created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating teacher', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing teacher
     */
    public function updateTeacher(UpdateTeacherRequest $request, $id)
    {
        try {
            $teacher = $this->teacherService->updateTeacher($id, $request->validated());
            if (!$teacher) {
                return $this->notFound('Teacher not found');
            }

            return $this->success($teacher, 'Teacher updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating teacher', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a teacher
     */
    public function deleteTeacher($id)
    {
        try {
            $result = $this->teacherService->deleteTeacher($id);
            if (!$result) {
                return $this->notFound('Teacher not found');
            }

            return $this->deleted('Teacher deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting teacher', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get all teachers with optional filters
     */
    public function getAllTeachers(Request $request)
    {
        try {
            $filters = $request->only(['name', 'email', 'status']);
            $teachers = $this->teacherService->getAllTeachers($filters);

            return $this->success($teachers);
        } catch (\Exception $e) {
            Log::error('Error fetching teachers', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of teachers
     */
    public function getTeachersPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $teachers = $this->teacherService->getTeachersPaginated($perPage, $filters);

            return $this->paginated($teachers);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated teachers', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }
}
