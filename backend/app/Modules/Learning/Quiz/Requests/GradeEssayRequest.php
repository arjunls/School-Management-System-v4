<?php

namespace App\Modules\Learning\Quiz\Requests;

use App\Http\Requests\ApiFormRequest;

class GradeEssayRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'score' => 'required|integer|min:0',
        ];
    }
}
