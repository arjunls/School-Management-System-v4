<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @group Academic Years
 *
 * APIs for managing academic years
 */
class AcademicYearController extends Controller
{
    public function __construct(protected AcademicYearService $academicYearService) {}

    /**
     * Get all academic years
     */
    public function getAll()
    {
        return response()->json([
            'success' => true,
            'data' => $this->academicYearService->getAll(),
        ]);
    }

    /**
     * Get paginated list of academic years
     */
    public function paginate(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search']);
        return response()->json([
            'success' => true,
            'data' => $this->academicYearService->paginate($perPage, $filters),
        ]);
    }

    /**
     * Find an academic year by ID
     */
    public function find(int $id)
    {
        $year = $this->academicYearService->find($id);
        if (!$year) {
            return response()->json(['success' => false, 'message' => 'Academic year not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $year]);
    }

    /**
     * Create a new academic year
     */
    public function create(Request $request)
    {
        try {
            $year = $this->academicYearService->create($request->only([
                'name', 'start_date', 'end_date', 'is_active'
            ]));
            return response()->json(['success' => true, 'data' => $year, 'message' => 'Academic year created'], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Update an academic year
     */
    public function update(Request $request, int $id)
    {
        try {
            $year = $this->academicYearService->update($id, $request->only([
                'name', 'start_date', 'end_date', 'is_active'
            ]));
            if (!$year) {
                return response()->json(['success' => false, 'message' => 'Academic year not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $year, 'message' => 'Academic year updated']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Delete an academic year
     */
    public function delete(int $id)
    {
        if ($this->academicYearService->delete($id)) {
            return response()->json(['success' => true, 'message' => 'Academic year deleted']);
        }
        return response()->json(['success' => false, 'message' => 'Academic year not found'], 404);
    }

    /**
     * Get the active academic year
     */
    public function getActive()
    {
        $year = $this->academicYearService->getActive();
        if (!$year) {
            return response()->json(['success' => false, 'message' => 'No active academic year'], 404);
        }
        return response()->json(['success' => true, 'data' => $year]);
    }

    // Terms
    /**
     * Get terms for an academic year
     */
    public function getTerms(int $academicYearId)
    {
        return response()->json([
            'success' => true,
            'data' => $this->academicYearService->getTerms($academicYearId),
        ]);
    }

    /**
     * Create a new term
     */
    public function createTerm(Request $request)
    {
        try {
            $term = $this->academicYearService->createTerm($request->only([
                'academic_year_id', 'name', 'start_date', 'end_date', 'is_active'
            ]));
            return response()->json(['success' => true, 'data' => $term, 'message' => 'Term created'], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Update a term
     */
    public function updateTerm(Request $request, int $id)
    {
        try {
            $term = $this->academicYearService->updateTerm($id, $request->only([
                'academic_year_id', 'name', 'start_date', 'end_date', 'is_active'
            ]));
            if (!$term) {
                return response()->json(['success' => false, 'message' => 'Term not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $term, 'message' => 'Term updated']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Delete a term
     */
    public function deleteTerm(int $id)
    {
        if ($this->academicYearService->deleteTerm($id)) {
            return response()->json(['success' => true, 'message' => 'Term deleted']);
        }
        return response()->json(['success' => false, 'message' => 'Term not found'], 404);
    }
}
