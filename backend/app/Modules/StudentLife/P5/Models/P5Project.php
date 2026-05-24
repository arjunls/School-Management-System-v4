<?php namespace App\Modules\StudentLife\P5\Models;
use App\Models\User; use App\Modules\Academic\Class\Models\Kelas;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class P5Project extends Model {
    protected $fillable=['title','description','theme','dimension','class_id','start_date','end_date','status','coordinator_id'];
    protected $casts=['start_date'=>'date','end_date'=>'date'];
    public function class(): BelongsTo{return $this->belongsTo(Kelas::class);}
    public function coordinator(): BelongsTo{return $this->belongsTo(User::class);}
    public function activities(): HasMany{return $this->hasMany(P5Activity::class,'p5_project_id');}
}
class P5Activity extends Model {
    protected $fillable=['p5_project_id','name','description','date','location','documentation'];
    protected $casts=['date'=>'date'];
    public function project(): BelongsTo{return $this->belongsTo(P5Project::class);}
}
