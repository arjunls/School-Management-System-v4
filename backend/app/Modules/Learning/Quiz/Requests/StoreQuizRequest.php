<?php

namespace App\Modules\Learning\Quiz\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreQuizRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
        ];
    }
}
