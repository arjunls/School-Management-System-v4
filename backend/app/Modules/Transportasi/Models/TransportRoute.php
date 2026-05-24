<?php

namespace App\Modules\Transportasi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    protected $table = 'transportation_routes';

    protected $fillable = [
        'name', 'description', 'pickup_point', 'dropoff_point', 'distance_km',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(TransportStudent::class, 'route_id');
    }
}
