<?php namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PollOption extends Model { protected $table='poll_options';protected $fillable=['poll_id','label','votes'];
protected $casts=['votes'=>'integer']; }
