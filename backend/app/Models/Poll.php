<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class Poll extends Model { protected $fillable=['title','description','start_at','end_at','is_active'];
protected $casts=['is_active'=>'boolean','start_at'=>'date','end_at'=>'date'];
public function options(): HasMany{return $this->hasMany(PollOption::class);} }
