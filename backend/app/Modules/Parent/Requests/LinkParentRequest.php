<?php

namespace App\Modules\Parent\Requests;

use App\Http\Requests\ApiFormRequest;

class LinkParentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:users,id',
            'relationship' => 'nullable|string|max:50',
        ];
    }
}
