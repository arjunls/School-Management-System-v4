<?php

namespace App\Modules\Auth\Requests;

use App\Http\Requests\ApiFormRequest;

class ResetPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
