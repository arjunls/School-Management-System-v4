<?php

namespace App\Modules\Schedule\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreScheduleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'class_id' => 'required|integer|exists:kelas,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'teacher_id' => 'nullable|integer|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ];
    }
}
