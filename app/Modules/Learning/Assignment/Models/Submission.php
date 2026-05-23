<?php

namespace App\Modules\Learning\Assignment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    public $timestamps = false;
    protected $fillable = ['assignment_id', 'student_id', 'notes', 'file_path', 'score', 'feedback', 'graded_at', 'submitted_at'];

    protected $casts = ['submitted_at' => 'datetime', 'graded_at' => 'datetime'];

    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class); }
}
