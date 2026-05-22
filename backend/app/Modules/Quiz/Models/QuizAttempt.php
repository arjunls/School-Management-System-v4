<?php

namespace App\Modules\Quiz\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'started_at', 'submitted_at', 'score', 'status'];
    protected $casts = ['started_at' => 'datetime', 'submitted_at' => 'datetime', 'score' => 'integer'];

    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function answers(): HasMany { return $this->hasMany(QuizAnswer::class, 'attempt_id'); }

    public function calculateScore(): int
    {
        $total = 0;
        foreach ($this->answers as $ans) {
            $q = $ans->question;
            if ($q->type === 'multiple_choice') {
                if ($ans->answer_text === $q->correct_answer) {
                    $total += $q->points;
                }
            } else {
                $total += $ans->score ?? 0;
            }
        }
        return $total;
    }
}
