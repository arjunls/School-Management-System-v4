<?php

namespace App\Modules\StaffManagement\Teacher\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreTeacherRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'photo' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }
}
