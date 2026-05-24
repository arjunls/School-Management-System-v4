<?php

namespace App\Modules\StudentLife\Extracurricular\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentLife\Extracurricular\Models\Extracurricular;
use Illuminate\Http\Request;
use App\Modules\StudentLife\Extracurricular\Requests\StoreExtracurricularRequest;

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
        return $this->success($query->get());
    }

    /**
     * Create a new extracurricular
     */
    public function store(StoreExtracurricularRequest $request)
    {
        $ec = Extracurricular::create($request->validated());
        return $this->created($ec, 'Extracurricular created');
    }

    /**
     * Update an extracurricular
     */
    public function update(Request $request, int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $ec->update($request->only(['name', 'description', 'coach', 'day', 'start_time', 'end_time', 'location', 'max_participants']));
        return $this->success($ec, 'Updated');
    }

    /**
     * Delete an extracurricular
     */
    public function destroy(int $id)
    {
        Extracurricular::findOrFail($id)->delete();
        return $this->deleted('Deleted');
    }

    /**
     * Join an extracurricular
     */
    public function join(Request $request, int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'student') return $this->error('Only students', 403);

        if ($ec->activeParticipants()->count() >= ($ec->max_participants ?? 9999)) {
            return $this->error('Full', 400);
        }

        if ($ec->activeParticipants()->where('student_id', $user->id)->exists()) {
            return $this->error('Already joined', 400);
        }

        $ec->participants()->attach($user->id, ['joined_at' => now()->format('Y-m-d'), 'status' => 'active']);
        return $this->success(null, 'Joined');
    }

    /**
     * Leave an extracurricular
     */
    public function leave(int $id)
    {
        $ec = Extracurricular::findOrFail($id);
        $user = request()->user();

        $ec->participants()->updateExistingPivot($user->id, ['status' => 'inactive']);
        return $this->success(null, 'Left');
    }
}
