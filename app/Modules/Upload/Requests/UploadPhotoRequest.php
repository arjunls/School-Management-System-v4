<?php

namespace App\Modules\Upload\Requests;

use App\Http\Requests\ApiFormRequest;

class UploadPhotoRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
