<?php namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PollVote extends Model { protected $table='poll_votes';protected $fillable=['poll_id','option_id','user_id']; }
