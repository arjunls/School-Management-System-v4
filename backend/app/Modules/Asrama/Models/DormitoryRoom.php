<?php

namespace App\Modules\Asrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DormitoryRoom extends Model
{
    protected $fillable = [
        'dormitory_id', 'name', 'capacity', 'floor',
    ];

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DormitoryAssignment::class, 'room_id');
    }
}
