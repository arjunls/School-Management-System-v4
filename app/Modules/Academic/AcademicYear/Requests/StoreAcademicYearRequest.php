<?php

namespace App\Modules\Academic\AcademicYear\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreAcademicYearRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ];
    }
}
