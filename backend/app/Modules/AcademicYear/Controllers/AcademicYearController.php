<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Requests\StoreAcademicYearRequest;
use App\Modules\AcademicYear\Requests\UpdateAcademicYearRequest;
use App\Modules\AcademicYear\Requests\StoreTermRequest;
use App\Modules\AcademicYear\Requests\UpdateTermRequest;
use App\Modules\AcademicYear\Services\AcademicYearService;
use Illuminate\Http\Request;

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
        return $this->success($this->academicYearService->getAll());
    }

    /**
     * Get paginated list of academic years
     */
    public function paginate(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search']);
        return $this->paginated($this->academicYearService->paginate($perPage, $filters));
    }

    /**
     * Find an academic year by ID
     */
    public function find(int $id)
    {
        $year = $this->academicYearService->find($id);
        if (!$year) {
            return $this->notFound('Academic year not found');
        }
        return $this->success($year);
    }

    /**
     * Create a new academic year
     */
    public function create(StoreAcademicYearRequest $request)
    {
        try {
            $year = $this->academicYearService->create($request->validated());
            return $this->created($year, 'Academic year created');
        } catch (\Exception $e) {
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an academic year
     */
    public function update(UpdateAcademicYearRequest $request, int $id)
    {
        try {
            $year = $this->academicYearService->update($id, $request->validated());
            if (!$year) {
                return $this->notFound('Academic year not found');
            }
            return $this->success($year, 'Academic year updated');
        } catch (\Exception $e) {
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete an academic year
     */
    public function delete(int $id)
    {
        if ($this->academicYearService->delete($id)) {
            return $this->deleted('Academic year deleted');
        }
        return $this->notFound('Academic year not found');
    }

    /**
     * Get the active academic year
     */
    public function getActive()
    {
        $year = $this->academicYearService->getActive();
        if (!$year) {
            return $this->notFound('No active academic year');
        }
        return $this->success($year);
    }

    // Terms
    /**
     * Get terms for an academic year
     */
    public function getTerms(int $academicYearId)
    {
        return $this->success($this->academicYearService->getTerms($academicYearId));
    }

    /**
     * Create a new term
     */
    public function createTerm(StoreTermRequest $request)
    {
        try {
            $term = $this->academicYearService->createTerm($request->validated());
            return $this->created($term, 'Term created');
        } catch (\Exception $e) {
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update a term
     */
    public function updateTerm(UpdateTermRequest $request, int $id)
    {
        try {
            $term = $this->academicYearService->updateTerm($id, $request->validated());
            if (!$term) {
                return $this->notFound('Term not found');
            }
            return $this->success($term, 'Term updated');
        } catch (\Exception $e) {
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a term
     */
    public function deleteTerm(int $id)
    {
        if ($this->academicYearService->deleteTerm($id)) {
            return $this->deleted('Term deleted');
        }
        return $this->notFound('Term not found');
    }
}
