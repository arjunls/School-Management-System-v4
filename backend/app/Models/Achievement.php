<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Achievement extends Model { protected $fillable = ['student_id','title','type','level','rank','description','certificate_file','achievement_date'];
public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); } }
