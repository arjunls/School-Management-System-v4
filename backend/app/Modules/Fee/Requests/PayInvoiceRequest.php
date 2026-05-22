<?php

namespace App\Modules\Fee\Requests;

use App\Http\Requests\ApiFormRequest;

class PayInvoiceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,cheque,other',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
