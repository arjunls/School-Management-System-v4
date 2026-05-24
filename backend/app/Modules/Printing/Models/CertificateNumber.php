<?php

namespace App\Modules\Printing\Models;

use App\Models\User;
use App\Modules\Academic\AcademicYear\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;

class CertificateNumber extends Model
{
    protected $table = 'certificate_numbers';

    protected $fillable = [
        'student_id', 'type', 'certificate_number',
        'academic_year_id', 'generated_at', 'generated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
