<?php

namespace App\Modules\Academic\Class\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Academic\Class\Requests\StoreClassRequest;
use App\Modules\Academic\Class\Requests\UpdateClassRequest;
use App\Modules\Academic\Class\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                return $this->notFound('Class not found');
            }

            return $this->success($class);
        } catch (\Exception $e) {
            Log::error('Error fetching class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new class
     */
    public function createClass(StoreClassRequest $request)
    {
        try {
            $class = $this->classService->createClass($request->validated());

            return $this->created($class, 'Class created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing class
     */
    public function updateClass(UpdateClassRequest $request, $id)
    {
        try {
            $class = $this->classService->updateClass($id, $request->validated());
            if (!$class) {
                return $this->notFound('Class not found');
            }

            return $this->success($class, 'Class updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
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
                return $this->notFound('Class not found');
            }

            return $this->deleted('Class deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
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

            return $this->success($classes);
        } catch (\Exception $e) {
            Log::error('Error fetching classes', ['exception' => $e]);

            return $this->error('Internal server error', 500);
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

            return $this->paginated($classes);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated classes', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Add a student to a class
     */
    public function addStudentToClass(Request $request, $classId, $studentId)
    {
        try {
            $result = $this->classService->addStudentToClass($classId, $studentId);

            return $this->success($result, 'Student added to class successfully');
        } catch (\Exception $e) {
            Log::error('Error adding student to class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Remove a student from a class
     */
    public function removeStudentFromClass(Request $request, $classId, $studentId)
    {
        try {
            $result = $this->classService->removeStudentFromClass($classId, $studentId);

            return $this->success($result, 'Student removed from class successfully');
        } catch (\Exception $e) {
            Log::error('Error removing student from class', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get all students in a class
     */
    public function getClassStudents($classId)
    {
        try {
            $students = $this->classService->getClassStudents($classId);

            return $this->success($students);
        } catch (\Exception $e) {
            Log::error('Error fetching class students', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }
}
