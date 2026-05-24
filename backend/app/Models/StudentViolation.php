<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StudentViolation extends Model { protected $table='student_violations'; protected $fillable=['student_id','violation_id','recorded_by','incident_date','description','action_taken','status'];
public function student(): BelongsTo{return $this->belongsTo(User::class,'student_id');}
public function violation(): BelongsTo{return $this->belongsTo(Violation::class);} }
