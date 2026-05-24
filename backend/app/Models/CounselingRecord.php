<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CounselingRecord extends Model { protected $fillable=['student_id','counselor_id','session_date','category','issue','action','notes','is_confidential'];
public function student(): BelongsTo{return $this->belongsTo(User::class,'student_id');}
public function counselor(): BelongsTo{return $this->belongsTo(User::class,'counselor_id');} }
