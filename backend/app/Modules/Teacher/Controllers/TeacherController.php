<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Teacher\Services\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Modules\Teacher\Requests\GetTeacherByEmailRequest;
use Illuminate\Validation\ValidationException;

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
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $teacher
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching teacher', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $teacher
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching teacher by email', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create a new teacher
     */
    public function createTeacher(Request $request)
    {
        try {
            $teacher = $this->teacherService->createTeacher($request->only([
                'name', 'email', 'password', 'password_confirmation', 'phone',
                'address', 'date_of_birth', 'gender', 'photo', 'status'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'data' => $teacher
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error creating teacher', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing teacher
     */
    public function updateTeacher(Request $request, $id)
    {
        try {
            $teacher = $this->teacherService->updateTeacher($id, $request->only([
                'name', 'email', 'phone', 'address', 'date_of_birth', 'gender',
                'photo', 'status', 'password', 'password_confirmation'
            ]));
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Teacher updated successfully',
                'data' => $teacher
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error updating teacher', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Teacher deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting teacher', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
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

            return response()->json([
                'success' => true,
                'data' => $teachers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching teachers', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
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

            return response()->json([
                'success' => true,
                'data' => $teachers->items(),
                'pagination' => [
                    'total' => $teachers->total(),
                    'per_page' => $teachers->perPage(),
                    'current_page' => $teachers->currentPage(),
                    'last_page' => $teachers->lastPage(),
                    'from' => $teachers->firstItem(),
                    'to' => $teachers->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated teachers', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
