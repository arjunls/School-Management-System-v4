<?php

namespace App\Modules\Auth\Requests;

use App\Http\Requests\ApiFormRequest;

class RegisterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:student,teacher',
        ];
    }
}
