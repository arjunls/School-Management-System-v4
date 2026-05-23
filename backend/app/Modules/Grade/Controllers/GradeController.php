<?php

namespace App\Modules\Grade\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Requests\StoreGradeRequest;
use App\Modules\Grade\Requests\UpdateGradeRequest;
use App\Modules\Grade\Services\GradeService;
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
            return response()->json([
                'success' => true,
                'data' => $this->gradeService->getAllGrades($request->only([
                    'student_id', 'subject_id', 'term'
                ])),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching grades', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
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

            return response()->json([
                'success' => true,
                'data' => $grades->items(),
                'pagination' => [
                    'total' => $grades->total(),
                    'per_page' => $grades->perPage(),
                    'current_page' => $grades->currentPage(),
                    'last_page' => $grades->lastPage(),
                    'from' => $grades->firstItem(),
                    'to' => $grades->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated grades', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
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
                return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $grade]);
        } catch (\Exception $e) {
            Log::error('Error fetching grade', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Create a new grade entry
     */
    public function createGrade(StoreGradeRequest $request)
    {
        try {
            $grade = $this->gradeService->createGrade($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Grade created successfully',
                'data' => $grade,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating grade', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
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
                return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Grade updated successfully', 'data' => $grade]);
        } catch (\Exception $e) {
            Log::error('Error updating grade', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
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
                return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Grade deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting grade', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }
}
