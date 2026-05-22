<?php

namespace App\Modules\ExamSchedule\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreExamScheduleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'nullable|string|max:50',
            'type' => 'nullable|in:midterm,final,quiz,other',
        ];
    }
}
