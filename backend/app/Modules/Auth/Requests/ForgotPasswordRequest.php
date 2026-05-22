<?php

namespace App\Modules\Auth\Requests;

use App\Http\Requests\ApiFormRequest;

class ForgotPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
        ];
    }
}
