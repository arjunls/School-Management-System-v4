<?php

namespace App\Modules\Tefa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TefaProduction extends Model
{
    protected $fillable = [
        'batch_no', 'product_id', 'quantity', 'status',
        'production_date', 'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'production_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(TefaProduct::class, 'product_id');
    }
}
