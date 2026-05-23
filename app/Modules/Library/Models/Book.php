<?php

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'publisher', 'published_year', 'category', 'description', 'total_copies', 'available_copies', 'location'];

    protected $casts = ['published_year' => 'integer', 'total_copies' => 'integer', 'available_copies' => 'integer'];

    public function loans(): HasMany { return $this->hasMany(BookLoan::class); }
}
