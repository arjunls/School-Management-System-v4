<?php

namespace App\Modules\Koperasi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CooperativeSale extends Model
{
    protected $table = 'cooperative_sales';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quantity',
        'total_price',
        'buyer_id',
        'sold_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CooperativeProduct::class, 'product_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sold_at', now()->toDateString());
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('sold_at', [$start, $end]);
    }
}
