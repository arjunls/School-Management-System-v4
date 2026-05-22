<?php

namespace App\Modules\Announcement\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreAnnouncementRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'required|in:all,admin,teacher,student,parent',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
        ];
    }
}
