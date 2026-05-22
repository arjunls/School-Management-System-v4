<?php

namespace App\Modules\Quiz\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question_text', 'type', 'options', 'correct_answer', 'points'];
    protected $casts = ['options' => 'array', 'points' => 'integer'];

    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }
}
