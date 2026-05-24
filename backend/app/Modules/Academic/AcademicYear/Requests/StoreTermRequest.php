<?php

namespace App\Modules\Academic\AcademicYear\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreTermRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
}
