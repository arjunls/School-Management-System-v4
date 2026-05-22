<?php

namespace App\Modules\Quiz\Requests;

use App\Http\Requests\ApiFormRequest;

class AddQuestionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
        ];
    }
}
