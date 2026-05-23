<?php

namespace App\Modules\Library\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateBookRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'author' => 'string|max:255',
            'isbn' => 'string|max:20|unique:books,isbn,' . $this->route('id'),
            'total_copies' => 'integer|min:1',
        ];
    }
}
