<?php

namespace App\Modules\Upload\Requests;

use App\Http\Requests\ApiFormRequest;

class UploadDocumentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240',
        ];
    }
}
