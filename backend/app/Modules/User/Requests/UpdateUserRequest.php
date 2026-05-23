<?php

namespace App\Modules\User\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|string|in:admin,teacher,student,parent,staff',
        ];
    }
}
