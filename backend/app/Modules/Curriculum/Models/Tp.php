<?php

namespace App\Modules\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tp extends Model
{
    protected $table = 'tp';

    protected $fillable = [
        'cp_id', 'code', 'description', 'order',
    ];

    public function cp(): BelongsTo
    {
        return $this->belongsTo(Cp::class, 'cp_id');
    }

    public function atps(): HasMany
    {
        return $this->hasMany(Atp::class, 'tp_id')->orderBy('order');
    }
}
