<?php

namespace App\Modules\Quiz\Models;

use App\Models\User;
use App\Modules\Subject\Models\Subject;
use App\Modules\Class\Models\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    protected $fillable = ['title', 'description', 'class_id', 'subject_id', 'teacher_id', 'time_limit', 'passing_score', 'due_date', 'status'];
    protected $casts = ['due_date' => 'datetime', 'time_limit' => 'integer', 'passing_score' => 'integer'];

    public function questions(): HasMany { return $this->hasMany(QuizQuestion::class); }
    public function attempts(): HasMany { return $this->hasMany(QuizAttempt::class); }
    public function class(): BelongsTo { return $this->belongsTo(Kelas::class, 'class_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }

    public function totalPoints(): int { return $this->questions->sum('points'); }
}
