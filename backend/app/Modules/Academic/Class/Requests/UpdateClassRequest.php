<?php

namespace App\Modules\Academic\Class\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateClassRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:50',
            'grade_level' => 'sometimes|required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'sometimes|nullable|integer|exists:users,id',
            'capacity' => 'sometimes|nullable|integer|min:1|max:100',
        ];
    }
}
