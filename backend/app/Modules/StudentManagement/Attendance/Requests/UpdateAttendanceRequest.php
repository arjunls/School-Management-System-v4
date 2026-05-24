<?php

namespace App\Modules\StudentManagement\Attendance\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateAttendanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|in:present,absent,sick,leave',
            'notes' => 'sometimes|nullable|string|max:500',
        ];
    }
}
