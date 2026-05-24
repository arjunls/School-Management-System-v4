<?php

namespace App\Modules\Ppdb\Models;

use App\Kernel\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpdbPeriod extends Model
{
    protected $table = 'ppdb_periods';

    protected $fillable = [
        'name',
        'academic_year',
        'start_date',
        'end_date',
        'quota',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'quota' => 'integer',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(PpdbApplicant::class, 'period_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function getApplicantCountAttribute()
    {
        return $this->applicants()->count();
    }

    public function getIsOpenAttribute()
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }
}
