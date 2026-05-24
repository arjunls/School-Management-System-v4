<?php

namespace App\Modules\Activity\Controllers;

use App\Kernel\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }
        if ($logName = $request->get('log_name')) {
            $query->where('log_name', $logName);
        }
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $activities = $query->paginate(50);
        $logNames = Activity::select('log_name')->distinct()->pluck('log_name');

        return view('activity.index', compact('activities', 'logNames'));
    }
}
