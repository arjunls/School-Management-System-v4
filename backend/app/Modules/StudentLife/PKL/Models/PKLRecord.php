<?php namespace App\Modules\StudentLife\PKL\Models;
use App\Models\User; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PKLRecord extends Model {
    protected $fillable=['student_id','company_name','company_address','supervisor_name','supervisor_phone','start_date','end_date','status','notes'];
    protected $casts=['start_date'=>'date','end_date'=>'date'];
    public function student(): BelongsTo{return $this->belongsTo(User::class);}
}
