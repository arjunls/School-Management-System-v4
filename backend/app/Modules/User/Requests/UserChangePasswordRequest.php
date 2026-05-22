<?php

namespace App\Modules\User\Requests;

use App\Http\Requests\ApiFormRequest;

class UserChangePasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }
}
