<?php

namespace App\Modules\Subject\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Requests\StoreSubjectRequest;
use App\Modules\Subject\Requests\UpdateSubjectRequest;
use App\Modules\Subject\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Subjects
 *
 * APIs for managing subjects
 */
class SubjectController extends Controller
{
    public function __construct(protected SubjectService $subjectService) {}

    /**
     * Get all subjects with optional filters
     */
    public function getAllSubjects(Request $request)
    {
        try {
            return $this->success($this->subjectService->getAllSubjects($request->only([
                'name', 'code', 'teacher_id'
            ])));
        } catch (\Exception $e) {
            Log::error('Error fetching subjects', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of subjects
     */
    public function getSubjectsPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $subjects = $this->subjectService->getSubjectsPaginated($perPage, $filters);

            return $this->paginated($subjects);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated subjects', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get a subject by ID
     */
    public function getSubject($id)
    {
        try {
            $subject = $this->subjectService->getSubject((int) $id);
            if (! $subject) {
                return $this->notFound('Subject not found');
            }

            return $this->success($subject);
        } catch (\Exception $e) {
            Log::error('Error fetching subject', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new subject
     */
    public function createSubject(StoreSubjectRequest $request)
    {
        try {
            $subject = $this->subjectService->createSubject($request->validated());

            return $this->created($subject, 'Subject created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating subject', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing subject
     */
    public function updateSubject(UpdateSubjectRequest $request, $id)
    {
        try {
            $subject = $this->subjectService->updateSubject((int) $id, $request->validated());
            if (! $subject) {
                return $this->notFound('Subject not found');
            }

            return $this->success($subject, 'Subject updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating subject', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a subject
     */
    public function deleteSubject($id)
    {
        try {
            $result = $this->subjectService->deleteSubject((int) $id);
            if (! $result) {
                return $this->notFound('Subject not found');
            }

            return $this->deleted('Subject deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting subject', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }
}
