<?php

namespace App\Modules\Industry\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndustryProgram extends Model
{
    protected $fillable = [
        'partner_id', 'name', 'description', 'duration_months',
        'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(IndustryPartner::class, 'partner_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(IndustryStudent::class, 'program_id');
    }
}
