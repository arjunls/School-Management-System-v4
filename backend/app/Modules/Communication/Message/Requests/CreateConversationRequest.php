<?php

namespace App\Modules\Communication\Message\Requests;

use App\Http\Requests\ApiFormRequest;

class CreateConversationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:users,id',
            'subject' => 'nullable|string|max:255',
        ];
    }
}
