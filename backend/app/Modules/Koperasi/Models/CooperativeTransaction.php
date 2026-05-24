<?php

namespace App\Modules\Koperasi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CooperativeTransaction extends Model
{
    protected $table = 'cooperative_transactions';

    protected $fillable = [
        'saving_id',
        'amount',
        'type',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function saving(): BelongsTo
    {
        return $this->belongsTo(CooperativeSaving::class, 'saving_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
