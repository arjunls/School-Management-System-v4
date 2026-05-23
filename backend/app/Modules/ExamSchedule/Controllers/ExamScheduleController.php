<?php

namespace App\Modules\ExamSchedule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ExamSchedule\Models\ExamSchedule;
use Illuminate\Http\Request;
use App\Modules\ExamSchedule\Requests\StoreExamScheduleRequest;

/**
 * @group Exam Schedules
 *
 * APIs for managing exam schedules
 */
class ExamScheduleController extends Controller
{
    /**
     * List all exam schedules
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ExamSchedule::with(['subject:id,name', 'class:id,name', 'teacher:id,name']);

        if ($user->role === 'teacher') $query->where('teacher_id', $user->id);
        elseif ($user->role === 'student') $query->whereHas('class.students', fn($q) => $q->where('users.id', $user->id));

        if ($request->class_id) $query->where('class_id', $request->class_id);
        if ($request->type) $query->where('type', $request->type);
        if ($request->month) $query->whereMonth('exam_date', $request->month);

        return $this->success($query->orderBy('exam_date')->orderBy('start_time')->get());
    }

    /**
     * Create a new exam schedule
     */
    public function store(StoreExamScheduleRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = $request->user()->id;
        $exam = ExamSchedule::create($data);
        return $this->created($exam, 'Exam scheduled');
    }

    /**
     * Update an exam schedule
     */
    public function update(Request $request, int $id)
    {
        $exam = ExamSchedule::findOrFail($id);
        $exam->update($request->only(['name', 'description', 'exam_date', 'start_time', 'end_time', 'room', 'type']));
        return $this->success($exam, 'Exam updated');
    }

    /**
     * Delete an exam schedule
     */
    public function destroy(int $id)
    {
        ExamSchedule::findOrFail($id)->delete();
        return $this->deleted('Exam deleted');
    }
}
