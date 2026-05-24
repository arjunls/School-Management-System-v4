<?php

namespace App\Modules\Learning\Grade\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Learning\Grade\Requests\StoreGradeRequest;
use App\Modules\Learning\Grade\Requests\UpdateGradeRequest;
use App\Modules\Learning\Grade\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Grades
 *
 * APIs for managing grades
 */
class GradeController extends Controller
{
    public function __construct(protected GradeService $gradeService) {}

    /**
     * Get all grades with optional filters
     */
    public function getAllGrades(Request $request)
    {
        try {
            return $this->success($this->gradeService->getAllGrades($request->only([
                'student_id', 'subject_id', 'term'
            ])));
        } catch (\Exception $e) {
            Log::error('Error fetching grades', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of grades
     */
    public function getGradesPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $grades = $this->gradeService->getGradesPaginated($perPage, $filters);

            return $this->paginated($grades);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated grades', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get a grade by ID
     */
    public function getGrade($id)
    {
        try {
            $grade = $this->gradeService->getGrade((int) $id);
            if (! $grade) {
                return $this->notFound('Grade not found');
            }

            return $this->success($grade);
        } catch (\Exception $e) {
            Log::error('Error fetching grade', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new grade entry
     */
    public function createGrade(StoreGradeRequest $request)
    {
        try {
            $grade = $this->gradeService->createGrade($request->validated());

            return $this->created($grade, 'Grade created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating grade', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing grade
     */
    public function updateGrade(UpdateGradeRequest $request, $id)
    {
        try {
            $grade = $this->gradeService->updateGrade((int) $id, $request->validated());
            if (! $grade) {
                return $this->notFound('Grade not found');
            }

            return $this->success($grade, 'Grade updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating grade', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a grade
     */
    public function deleteGrade($id)
    {
        try {
            $result = $this->gradeService->deleteGrade((int) $id);
            if (! $result) {
                return $this->notFound('Grade not found');
            }

            return $this->deleted('Grade deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting grade', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }
}
