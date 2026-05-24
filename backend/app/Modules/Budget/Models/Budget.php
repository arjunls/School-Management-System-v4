<?php

namespace App\Modules\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'planned_amount',
        'realized_amount', 'period', 'status',
    ];

    protected $casts = [
        'planned_amount' => 'decimal:2',
        'realized_amount' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'category_id');
    }
}
