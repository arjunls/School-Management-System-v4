<?php

namespace App\Modules\Bkk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobVacancy extends Model
{
    protected $fillable = [
        'company_id', 'title', 'description', 'requirements',
        'slots', 'closing_date', 'status',
    ];

    protected $casts = [
        'slots' => 'integer',
        'closing_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'vacancy_id');
    }
}
