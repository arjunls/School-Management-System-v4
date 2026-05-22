<?php

namespace App\Modules\Assignment\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreAssignmentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date|after:now',
            'max_score' => 'nullable|integer|min:1|max:1000',
        ];
    }
}
