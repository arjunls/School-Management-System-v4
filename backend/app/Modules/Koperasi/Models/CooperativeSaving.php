<?php

namespace App\Modules\Koperasi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CooperativeSaving extends Model
{
    protected $table = 'cooperative_savings';

    protected $fillable = [
        'user_id',
        'balance',
        'type',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CooperativeTransaction::class, 'saving_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
