<?php

namespace App\Modules\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atp extends Model
{
    protected $table = 'atp';

    protected $fillable = [
        'tp_id', 'objective', 'material', 'assessment',
        'method', 'hours', 'order',
    ];

    protected $casts = [
        'hours' => 'integer',
        'order' => 'integer',
    ];

    public function tp(): BelongsTo
    {
        return $this->belongsTo(Tp::class, 'tp_id');
    }
}
