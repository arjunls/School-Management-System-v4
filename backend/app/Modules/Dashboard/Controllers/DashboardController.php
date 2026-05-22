<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Dashboard
 *
 * APIs for dashboard statistics
 */
class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        try {
            $academicYearId = $request->input('academic_year_id');
            $stats = $this->dashboardService->getStats($academicYearId);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Get attendance chart data
     */
    public function getAttendanceChartData(Request $request)
    {
        try {
            $academicYearId = $request->input('academic_year_id');
            $days = $request->input('days', 7);
            $chartData = $this->dashboardService->getAttendanceChartData($academicYearId, $days);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance chart data', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Get performance chart data
     */
    public function getPerformanceChartData(Request $request)
    {
        try {
            $academicYearId = $request->input('academic_year_id');
            $chartData = $this->dashboardService->getPerformanceChartData($academicYearId);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching performance chart data', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Get student performance trend
     */
    public function getStudentPerformanceTrend(Request $request, int $studentId)
    {
        try {
            $academicYearId = $request->input('academic_year_id');
            $data = $this->dashboardService->getStudentPerformanceTrend($studentId, $academicYearId);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error fetching student performance trend', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }
}
