<?php

namespace App\Modules\Assignment\Models;

use App\Models\User;
use App\Modules\Class\Models\Kelas;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = ['title', 'description', 'class_id', 'subject_id', 'teacher_id', 'due_date', 'attachment_path', 'max_score'];

    protected $casts = ['due_date' => 'datetime'];

    public function class(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class); }
    public function submissions(): HasMany { return $this->hasMany(Submission::class); }
}
