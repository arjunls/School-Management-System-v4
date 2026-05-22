<?php

namespace App\Modules\Message\Requests;

use App\Http\Requests\ApiFormRequest;

class SendMessageRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string|max:5000',
        ];
    }
}
