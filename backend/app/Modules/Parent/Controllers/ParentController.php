<?php

namespace App\Modules\Parent\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Modules\Parent\Requests\LinkParentRequest;
use App\Modules\Parent\Requests\UnlinkParentRequest;

/**
 * @group Parents
 *
 * APIs for managing parents
 */
class ParentController extends Controller
{
    /**
     * Get children of the authenticated parent
     */
    public function getChildren(Request $request)
    {
        $user = $request->user();
        $children = $user->children()->with('kelas')->get();
        return response()->json(['success' => true, 'data' => $children]);
    }

    /**
     * Link a parent to a student
     */
    public function linkParentToStudent(LinkParentRequest $request)
    {
        $data = $request->validated();
        $parent = User::findOrFail($data['parent_id']);
        $parent->children()->syncWithoutDetaching([
            $data['student_id'] => ['relationship' => $data['relationship'] ?? null],
        ]);

        return response()->json(['success' => true, 'message' => 'Parent linked to student']);
    }

    /**
     * Unlink a parent from a student
     */
    public function unlinkParentFromStudent(UnlinkParentRequest $request)
    {
        $data = $request->validated();
        $parent = User::findOrFail($data['parent_id']);
        $parent->children()->detach($data['student_id']);

        return response()->json(['success' => true, 'message' => 'Parent unlinked from student']);
    }

    /**
     * Get parents of a student
     */
    public function getStudentParents(int $studentId)
    {
        $student = User::findOrFail($studentId);
        return response()->json(['success' => true, 'data' => $student->parents]);
    }

    /**
     * Get grades for a student
     */
    public function getStudentGrade(Request $request, int $studentId)
    {
        $user = $request->user();
        if ($user->role === 'parent' && !$user->children()->where('student_id', $studentId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $student = User::with('kelas')->findOrFail($studentId);
        $grades = $student->grades ?? \App\Modules\Grade\Models\Grade::where('student_id', $studentId)->with('subject')->get();
        return response()->json(['success' => true, 'data' => ['student' => $student, 'grades' => $grades]]);
    }
}
