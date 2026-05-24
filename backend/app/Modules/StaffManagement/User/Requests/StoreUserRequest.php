<?php

namespace App\Modules\StaffManagement\User\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,teacher,student,parent,staff',
        ];
    }
}
