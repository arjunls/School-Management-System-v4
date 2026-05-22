<?php

namespace App\Modules\Class\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'name',
        'grade_level',
        'homeroom_teacher_id',
        'capacity',
    ];

    protected $casts = [
        'grade_level' => 'integer',
        'capacity' => 'integer',
    ];

    /**
     * Get the homeroom teacher (wali kelas) for this class.
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'homeroom_teacher_id');
    }

    /**
     * Get the students in this class.
     * Assumes students are users with role='student' and kelas_id set.
     */
    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'kelas_id')->where('role', 'student');
    }

    /**
     * Get the number of students currently in this class.
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Check if the class is full.
     */
    public function isFullAttribute()
    {
        return $this->getStudentCountAttribute() >= $this->capacity;
    }
}