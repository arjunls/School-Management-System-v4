<?php

namespace App\Modules\Finance\Fee\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateFeeTypeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'amount' => 'numeric|min:0',
            'frequency' => 'in:once,monthly,quarterly,yearly',
        ];
    }
}
