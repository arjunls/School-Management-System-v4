<?php

namespace App\Modules\Extracurricular\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreExtracurricularRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coach' => 'nullable|string|max:100',
            'day' => 'nullable|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
        ];
    }
}
