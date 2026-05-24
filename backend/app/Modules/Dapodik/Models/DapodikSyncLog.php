<?php

namespace App\Modules\Dapodik\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikSyncLog extends Model
{
    protected $table = 'dapodik_sync_logs';

    protected $fillable = [
        'sync_type',
        'status',
        'started_at',
        'completed_at',
        'records_processed',
        'message',
    ];
}
