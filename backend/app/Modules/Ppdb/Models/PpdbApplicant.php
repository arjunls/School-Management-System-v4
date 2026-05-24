<?php

namespace App\Modules\Ppdb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbApplicant extends Model
{
    protected $table = 'ppdb_applicants';

    protected $fillable = [
        'period_id',
        'registration_number',
        'full_name',
        'nisn',
        'birth_date',
        'birth_place',
        'gender',
        'religion',
        'address',
        'phone',
        'email',
        'previous_school',
        'father_name',
        'mother_name',
        'parent_phone',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriod::class, 'period_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }
}
