<?php

namespace App\Modules\Fee\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreInvoiceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'fee_type_id' => 'required|exists:fee_types,id',
            'student_id' => 'required|exists:users,id',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
