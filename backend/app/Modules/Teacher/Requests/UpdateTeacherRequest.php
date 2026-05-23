<?php

namespace App\Modules\Teacher\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateTeacherRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->route('id'),
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'photo' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|in:active,inactive,suspended',
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }
}
