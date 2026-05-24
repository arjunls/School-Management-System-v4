<?php

namespace App\Modules\Learning\Quiz\Requests;

use App\Http\Requests\ApiFormRequest;

class SubmitQuizRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.answer_text' => 'nullable|string',
        ];
    }
}
