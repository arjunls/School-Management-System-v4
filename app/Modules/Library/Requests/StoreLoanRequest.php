<?php

namespace App\Modules\Library\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreLoanRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ];
    }
}
