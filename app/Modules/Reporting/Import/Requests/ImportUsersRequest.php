<?php

namespace App\Modules\Reporting\Import\Requests;

use App\Http\Requests\ApiFormRequest;

class ImportUsersRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'nisn' => 'nullable|string|max:20|unique:users,nisn',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }
}
