<?php

namespace App\Modules\Ukk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificationSchema extends Model
{
    protected $table = 'certification_schemas';

    protected $fillable = [
        'name', 'field', 'description', 'level',
    ];

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'schema_id');
    }
}
