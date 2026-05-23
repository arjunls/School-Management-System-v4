<?php

namespace App\Modules\Academic\Subject\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreSubjectRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:20',
            'teacher_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
