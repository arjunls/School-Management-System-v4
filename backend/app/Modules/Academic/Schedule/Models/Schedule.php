<?php

namespace App\Modules\Academic\Schedule\Models;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'class_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time', 'room',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
