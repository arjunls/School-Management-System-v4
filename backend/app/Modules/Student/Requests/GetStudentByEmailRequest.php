<?php

namespace App\Modules\Student\Requests;

use App\Http\Requests\ApiFormRequest;

class GetStudentByEmailRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
        ];
    }
}
