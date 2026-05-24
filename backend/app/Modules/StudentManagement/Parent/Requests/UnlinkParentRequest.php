<?php

namespace App\Modules\StudentManagement\Parent\Requests;

use App\Http\Requests\ApiFormRequest;

class UnlinkParentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:users,id',
        ];
    }
}
