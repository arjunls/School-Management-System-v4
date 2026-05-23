<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Grade\Models\Grade;
use App\Modules\Subject\Models\Subject;
use Illuminate\Http\Request;

/**
 * @group Reports
 *
 * APIs for generating reports
 */
class ReportController extends Controller
{
    /**
     * Generate student report card
     */
    public function studentReportCard(Request $request, int $studentId)
    {
        $user = $request->user();
        if ($user->role === 'parent' && !$user->children()->where('student_id', $studentId)->exists()) {
            return $this->error('Forbidden', 403);
        }

        $student = User::with('kelas')->findOrFail($studentId);
        $grades = Grade::where('student_id', $studentId)
            ->with('subject')
            ->orderBy('term')
            ->get();

        $attendance = AttendanceRecord::where('student_id', $studentId)->get();
        $totalDays = $attendance->count();
        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $sickDays = $attendance->whereIn('status', ['sick', 'leave'])->count();

        $averageScore = $grades->avg('score');

        return $this->success([
            'student' => $student,
            'grades' => $grades->groupBy('term'),
            'attendance_summary' => [
                'total_days' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'sick' => $sickDays,
                'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0,
            ],
            'average_score' => $averageScore ? round($averageScore, 1) : null,
        ]);
    }

    /**
     * Generate attendance report
     */
    public function attendanceReport(Request $request)
    {
        $filters = $request->only(['class_id', 'start_date', 'end_date', 'student_id']);
        $query = AttendanceRecord::with('student.kelas');

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', fn($q) => $q->where('kelas_id', $filters['class_id']));
        }
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        $records = $query->orderBy('date')->get()->groupBy('student_id');

        $report = $records->map(function ($items, $studentId) {
            $student = $items->first()->student;
            $total = $items->count();
            $present = $items->where('status', 'present')->count();
            return [
                'student' => ['id' => $student->id, 'name' => $student->name, 'nisn' => $student->nisn, 'kelas' => $student->kelas?->name],
                'total_days' => $total,
                'present' => $present,
                'absent' => $items->where('status', 'absent')->count(),
                'sick' => $items->whereIn('status', ['sick', 'leave'])->count(),
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        })->values();

        return $this->success($report);
    }

    /**
     * Generate student transcript
     */
    public function transcript(int $studentId)
    {
        $grades = \App\Models\Grade::where('student_id', $studentId)
            ->with(['subject:id,name', 'class:id,name'])
            ->orderBy('created_at')
            ->get();

        $attendance = \App\Models\Attendance::where('student_id', $studentId)->get();
        $present = $attendance->where('status', 'present')->count();
        $totalAtt = max($attendance->count(), 1);

        $subjectSummary = $grades->groupBy('subject_id')->map(function ($items, $subjectId) {
            $subject = $items->first()->subject;
            $scores = $items->pluck('score')->filter();
            return [
                'subject' => $subject?->name ?? 'Unknown',
                'scores' => $scores->values(),
                'average' => $scores->avg() ? round($scores->avg(), 1) : null,
                'total' => $scores->sum(),
                'count' => $scores->count(),
            ];
        })->values();

        $overallAvg = $grades->whereNotNull('score')->avg('score');

        return $this->success([
            'student' => \App\Models\User::find($studentId),
            'grades_count' => $grades->count(),
            'subjects' => $subjectSummary,
            'overall_average' => $overallAvg ? round($overallAvg, 1) : null,
            'attendance_rate' => round(($present / $totalAtt) * 100, 1),
            'total_scores' => $grades->whereNotNull('score')->count(),
        ]);
    }
}
