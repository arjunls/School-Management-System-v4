<?php

namespace App\Modules\StaffManagement\User\Requests;

use App\Http\Requests\ApiFormRequest;

class GetUserByEmailRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
        ];
    }
}
