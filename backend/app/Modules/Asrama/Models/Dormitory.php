<?php

namespace App\Modules\Asrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dormitory extends Model
{
    protected $fillable = [
        'name', 'gender', 'capacity', 'supervisor_id', 'address',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(DormitoryRoom::class, 'dormitory_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'supervisor_id');
    }
}
