<?php

namespace App\Modules\Academic\Subject\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateSubjectRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:100',
            'code' => 'sometimes|required|string|max:20|unique:subjects,code,' . $this->route('id'),
            'description' => 'sometimes|nullable|string',
            'credits' => 'sometimes|nullable|integer|min:1|max:20',
            'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
        ];
    }
}
