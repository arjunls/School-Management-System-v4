<?php

namespace App\Modules\Ukk\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $fillable = [
        'schema_id', 'student_id', 'assessor_id', 'exam_date',
        'result', 'certificate_number', 'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function schema(): BelongsTo
    {
        return $this->belongsTo(CertificationSchema::class, 'schema_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
