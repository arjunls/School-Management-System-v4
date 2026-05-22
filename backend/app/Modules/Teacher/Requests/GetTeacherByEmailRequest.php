<?php

namespace App\Modules\Teacher\Requests;

use App\Http\Requests\ApiFormRequest;

class GetTeacherByEmailRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
        ];
    }
}
