<?php

namespace App\Modules\AcademicYear\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateTermRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id' => 'exists:academic_years,id',
            'name' => 'string|max:50',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
}
