<?php

namespace App\Modules\StudentManagement\Attendance\Models;

use App\Models\User;
use App\Modules\Academic\AcademicYear\Models\AcademicYear;
use App\Modules\Academic\AcademicYear\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $table = 'attendance_records';

    protected $fillable = [
        'student_id',
        'date',
        'status',
        'notes',
        'created_by',
        'academic_year_id',
        'term_id',
    ];

    protected $casts = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
