<?php namespace App\Modules\StudentLife\Alumni\Models;
use App\Models\User; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Alumni extends Model {
    protected $fillable=['student_id','graduation_year','final_status','current_occupation','current_company','current_education','phone','email','address','is_tracing_data_updated'];
    protected $casts=['is_tracing_data_updated'=>'boolean'];
    public function student(): BelongsTo{return $this->belongsTo(User::class);}
}
