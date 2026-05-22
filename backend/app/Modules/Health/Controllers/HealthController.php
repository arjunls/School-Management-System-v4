<?php

namespace App\Modules\Health\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Health\Models\HealthRecord;
use Illuminate\Http\Request;
use App\Modules\Health\Requests\UpsertHealthRequest;

/**
 * @group Health
 *
 * APIs for managing health records
 */
class HealthController extends Controller
{
    /**
     * Get health record for a student
     */
    public function show(int $studentId)
    {
        $record = HealthRecord::where('student_id', $studentId)->with('student:id,name,email')->first();
        if (!$record) {
            return response()->json(['success' => true, 'data' => null]);
        }
        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Create or update a health record
     */
    public function upsert(UpsertHealthRequest $request, int $studentId)
    {
        $record = HealthRecord::updateOrCreate(
            ['student_id' => $studentId],
            $request->validated()
        );
        return response()->json(['success' => true, 'data' => $record, 'message' => 'Health record saved']);
    }
}
