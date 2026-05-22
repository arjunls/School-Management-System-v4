<?php

namespace App\Modules\Health\Requests;

use App\Http\Requests\ApiFormRequest;

class UpsertHealthRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ];
    }
}
