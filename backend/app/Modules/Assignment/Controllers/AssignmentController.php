<?php

namespace App\Modules\Assignment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assignment\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Modules\Assignment\Requests\StoreAssignmentRequest;
use App\Modules\Assignment\Requests\SubmitAssignmentRequest;
use App\Modules\Assignment\Requests\GradeAssignmentRequest;

/**
 * @group Assignments
 *
 * APIs for managing assignments
 */
class AssignmentController extends Controller
{
    /**
     * List all assignments
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Assignment::with(['subject:id,name', 'class:id,name', 'teacher:id,name']);

        if ($user->role === 'teacher') $query->where('teacher_id', $user->id);
        elseif ($user->role === 'student') $query->whereHas('class.students', fn($q) => $q->where('users.id', $user->id));

        if ($request->class_id) $query->where('class_id', $request->class_id);
        if ($request->subject_id) $query->where('subject_id', $request->subject_id);

        return response()->json(['success' => true, 'data' => $query->orderByDesc('due_date')->get()]);
    }

    /**
     * Create a new assignment
     */
    public function store(StoreAssignmentRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = $request->user()->id;

        $assignment = Assignment::create($data);
        return response()->json(['success' => true, 'data' => $assignment, 'message' => 'Assignment created'], 201);
    }

    /**
     * Get an assignment by ID
     */
    public function show(int $id)
    {
        $assignment = Assignment::with(['subject', 'class', 'teacher', 'submissions.student'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $assignment]);
    }

    /**
     * Update an existing assignment
     */
    public function update(Request $request, int $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->only(['title', 'description', 'due_date', 'max_score']));
        return response()->json(['success' => true, 'data' => $assignment, 'message' => 'Assignment updated']);
    }

    /**
     * Delete an assignment
     */
    public function destroy(int $id)
    {
        Assignment::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Assignment deleted']);
    }

    /**
     * Submit an assignment
     */
    public function submit(SubmitAssignmentRequest $request, int $id)
    {
        $assignment = Assignment::findOrFail($id);
        $user = $request->user();

        $data = $request->validated();
        $data['submitted_at'] = now();
        $submission = $assignment->submissions()->updateOrCreate(
            ['student_id' => $user->id],
            $data
        );

        return response()->json(['success' => true, 'data' => $submission, 'message' => 'Submitted'], 201);
    }

    /**
     * Grade an assignment submission
     */
    public function grade(GradeAssignmentRequest $request, int $id, int $submissionId)
    {
        $assignment = Assignment::findOrFail($id);
        $submission = $assignment->submissions()->findOrFail($submissionId);

        $data = $request->validated();
        $data['graded_at'] = now();
        $submission->update($data);
        return response()->json(['success' => true, 'data' => $submission, 'message' => 'Graded']);
    }
}
