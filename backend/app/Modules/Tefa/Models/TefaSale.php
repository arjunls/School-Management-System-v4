<?php

namespace App\Modules\Tefa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TefaSale extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'total_price', 'customer_name', 'sale_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'sale_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(TefaProduct::class, 'product_id');
    }
}
