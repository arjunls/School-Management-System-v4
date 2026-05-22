<?php

namespace App\Modules\Assignment\Requests;

use App\Http\Requests\ApiFormRequest;

class SubmitAssignmentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
