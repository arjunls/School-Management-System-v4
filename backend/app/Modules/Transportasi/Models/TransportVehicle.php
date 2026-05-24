<?php

namespace App\Modules\Transportasi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportVehicle extends Model
{
    protected $table = 'transportation_vehicles';

    protected $fillable = [
        'name', 'plate_number', 'capacity', 'driver_name', 'driver_phone', 'status',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(TransportStudent::class, 'vehicle_id');
    }
}
