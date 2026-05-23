<?php

namespace App\Modules\Learning\Grade\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreGradeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:users,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:2',
            'term' => 'nullable|string|max:50',
        ];
    }
}
