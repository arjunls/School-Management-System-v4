<?php

namespace App\Modules\ExamSchedule\Models;

use App\Models\User;
use App\Modules\Class\Models\Kelas;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSchedule extends Model
{
    protected $fillable = ['name', 'description', 'class_id', 'subject_id', 'teacher_id', 'exam_date', 'start_time', 'end_time', 'room', 'type'];

    protected $casts = ['exam_date' => 'date'];

    public function class(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class); }
}
