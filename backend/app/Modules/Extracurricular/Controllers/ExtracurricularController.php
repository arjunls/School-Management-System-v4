<?php

namespace App\Modules\Extracurricular\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Extracurricular\Models\Extracurricular;
use Illuminate\Http\Request;
use App\Modules\Extracurricular\Requests\StoreExtracurricularRequest;

/**
 * @group Extracurriculars
 *
 * APIs for managing extracurriculars
 */
class ExtracurricularController extends Controller
{
    /**
     * List all extracurriculars
     */
    public function index(Request $request)
    {
        $query = Extracurricular::withCount('activeParticipants');
        $user = $request->user();
        if ($user->role === 'student') {
            $query->with(['activeParticipants' => fn($q) => $q->where('student_id', $user->id)]);
        }
        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    /**
     * Create a new extracurricular
     */
    public function store(StoreExtracurricularRequest $request)
    {
        $ec = Extracurricular::create($request->validated());
        return response()->json(['success' => true, 'data' => $ec, 'message' => 'Extracurricular created'], 201);
    }

    /**
     * Update an extracurricular
     */
    public function update(Request $request, int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $ec->update($request->only(['name', 'description', 'coach', 'day', 'start_time', 'end_time', 'location', 'max_participants']));
        return response()->json(['success' => true, 'data' => $ec, 'message' => 'Updated']);
    }

    /**
     * Delete an extracurricular
     */
    public function destroy(int $id)
    {
        Extracurricular::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    /**
     * Join an extracurricular
     */
    public function join(Request $request, int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'student') return response()->json(['success' => false, 'message' => 'Only students'], 403);

        if ($ec->activeParticipants()->count() >= ($ec->max_participants ?? 9999)) {
            return response()->json(['success' => false, 'message' => 'Full'], 400);
        }

        if ($ec->activeParticipants()->where('student_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already joined'], 400);
        }

        $ec->participants()->attach($user->id, ['joined_at' => now()->format('Y-m-d'), 'status' => 'active']);
        return response()->json(['success' => true, 'message' => 'Joined']);
    }

    /**
     * Leave an extracurricular
     */
    public function leave(int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $user = request()->user();

        $ec->participants()->updateExistingPivot($user->id, ['status' => 'inactive']);
        return response()->json(['success' => true, 'message' => 'Left']);
    }
}
