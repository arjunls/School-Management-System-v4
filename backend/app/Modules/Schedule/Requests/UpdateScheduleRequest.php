<?php

namespace App\Modules\Schedule\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateScheduleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'class_id' => 'sometimes|required|integer|exists:kelas,id',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
            'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'room' => 'sometimes|nullable|string|max:50',
        ];
    }
}
