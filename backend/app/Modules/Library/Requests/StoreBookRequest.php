<?php

namespace App\Modules\Library\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreBookRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1900|max:2027',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'total_copies' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:50',
        ];
    }
}
