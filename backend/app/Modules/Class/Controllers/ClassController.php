<?php

namespace App\Modules\Class\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Class\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group Classes
 *
 * APIs for managing classes
 */
class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * Get a class by ID
     */
    public function getClass($id)
    {
        try {
            $class = $this->classService->getClass($id);
            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $class
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create a new class
     */
    public function createClass(Request $request)
    {
        try {
            $class = $this->classService->createClass($request->only([
                'name', 'grade_level', 'homeroom_teacher_id', 'capacity'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Class created successfully',
                'data' => $class
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error creating class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing class
     */
    public function updateClass(Request $request, $id)
    {
        try {
            $class = $this->classService->updateClass($id, $request->only([
                'name', 'grade_level', 'homeroom_teacher_id', 'capacity'
            ]));
            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully',
                'data' => $class
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error updating class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a class
     */
    public function deleteClass($id)
    {
        try {
            $result = $this->classService->deleteClass($id);
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Class deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all classes with optional filters
     */
    public function getAllClasses(Request $request)
    {
        try {
            $filters = $request->only(['name', 'grade_level']);
            $classes = $this->classService->getAllClasses($filters);

            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching classes', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get paginated list of classes
     */
    public function getClassesPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $classes = $this->classService->getClassesPaginated($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => $classes->items(),
                'pagination' => [
                    'total' => $classes->total(),
                    'per_page' => $classes->perPage(),
                    'current_page' => $classes->currentPage(),
                    'last_page' => $classes->lastPage(),
                    'from' => $classes->firstItem(),
                    'to' => $classes->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated classes', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Add a student to a class
     */
    public function addStudentToClass(Request $request, $classId, $studentId)
    {
        try {
            $result = $this->classService->addStudentToClass($classId, $studentId);

            return response()->json([
                'success' => true,
                'message' => 'Student added to class successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding student to class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove a student from a class
     */
    public function removeStudentFromClass(Request $request, $classId, $studentId)
    {
        try {
            $result = $this->classService->removeStudentFromClass($classId, $studentId);

            return response()->json([
                'success' => true,
                'message' => 'Student removed from class successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing student from class', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all students in a class
     */
    public function getClassStudents($classId)
    {
        try {
            $students = $this->classService->getClassStudents($classId);

            return response()->json([
                'success' => true,
                'data' => $students
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching class students', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
