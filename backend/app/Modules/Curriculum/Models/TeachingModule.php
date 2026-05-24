<?php

namespace App\Modules\Curriculum\Models;

use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingModule extends Model
{
    protected $table = 'teaching_modules';

    protected $fillable = [
        'subject_id', 'class_id', 'title', 'content', 'file_path',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }
}
