<?php namespace App\Modules\Learning\TeachingLog\Models;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DailyTeachingLog extends Model {
    protected $fillable = ['teacher_id','class_id','subject_id','date','start_time','end_time','topic','material','notes','present_students','absent_students','cover_image'];
    protected $casts = ['date' => 'date'];
    public function teacher(): BelongsTo { return $this->belongsTo(User::class); }
    public function class(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
}
