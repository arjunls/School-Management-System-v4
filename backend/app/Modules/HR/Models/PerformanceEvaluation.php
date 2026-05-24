<?php

namespace App\Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceEvaluation extends Model
{
    use HasFactory;

    protected $table = 'performance_evaluations';

    protected $fillable = [
        'teacher_id',
        'evaluator_id',
        'type',
        'evaluation_date',
        'score',
        'notes',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'score' => 'decimal:2',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
