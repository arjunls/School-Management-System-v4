<?php

namespace App\Modules\Calendar\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreEventRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'type' => 'required|in:academic,holiday,exam,meeting,extracurricular,other',
        ];
    }
}
