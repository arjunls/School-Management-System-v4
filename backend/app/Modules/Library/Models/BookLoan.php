<?php

namespace App\Modules\Library\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookLoan extends Model
{
    protected $fillable = ['book_id', 'user_id', 'loan_date', 'due_date', 'returned_date', 'notes', 'status'];

    protected $casts = ['loan_date' => 'date', 'due_date' => 'date', 'returned_date' => 'date'];

    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
