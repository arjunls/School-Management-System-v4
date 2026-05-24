<?php

namespace App\Modules\Learning\Assignment\Requests;

use App\Http\Requests\ApiFormRequest;

class GradeAssignmentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'score' => 'required|integer|min:0|max:1000',
            'feedback' => 'nullable|string|max:2000',
        ];
    }
}
