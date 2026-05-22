<?php

namespace App\Modules\Calendar\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'location', 'color', 'type'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'start_time' => 'datetime:H:i', 'end_time' => 'datetime:H:i'];
}
