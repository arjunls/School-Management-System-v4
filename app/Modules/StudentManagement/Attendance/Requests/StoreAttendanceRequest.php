<?php

namespace App\Modules\StudentManagement\Attendance\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreAttendanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,sick,leave',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
