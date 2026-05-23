<?php

namespace App\Modules\Class\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreClassRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|integer|exists:users,id',
            'capacity' => 'nullable|integer|min:1|max:100',
        ];
    }
}
