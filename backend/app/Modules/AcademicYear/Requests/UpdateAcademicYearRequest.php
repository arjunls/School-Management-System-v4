<?php

namespace App\Modules\AcademicYear\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateAcademicYearRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:50',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
}
