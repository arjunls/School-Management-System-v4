<?php

namespace App\Modules\Grade\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateGradeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => 'sometimes|required|integer|exists:users,id',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'score' => 'sometimes|nullable|numeric|min:0|max:100',
            'grade' => 'sometimes|nullable|string|max:2',
            'term' => 'sometimes|nullable|string|max:50',
        ];
    }
}
