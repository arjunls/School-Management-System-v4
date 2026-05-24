<?php

namespace App\Modules\Curriculum\Models;

use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cp extends Model
{
    protected $table = 'cp';

    protected $fillable = [
        'subject_id', 'code', 'description', 'phase', 'class',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function tps(): HasMany
    {
        return $this->hasMany(Tp::class, 'cp_id')->orderBy('order');
    }
}
