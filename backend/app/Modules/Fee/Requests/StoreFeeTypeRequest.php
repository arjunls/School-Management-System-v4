<?php

namespace App\Modules\Fee\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreFeeTypeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:once,monthly,quarterly,yearly',
            'description' => 'nullable|string',
        ];
    }
}
