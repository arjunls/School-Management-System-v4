<?php

namespace App\Modules\Dashboard\Services;

use App\Models\User;
use App\Modules\Academic\AcademicYear\Models\AcademicYear;
use App\Modules\Academic\AcademicYear\Models\Term;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Learning\Grade\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(?int $academicYearId = null)
    {
        $students = User::where('role', 'student')->count();
        $teachers = User::where('role', 'teacher')->count();
        $classes = Kelas::count();

        $attQuery = AttendanceRecord::query();
        if ($academicYearId) $attQuery->where('academic_year_id', $academicYearId);
        $totalAttendance = $attQuery->count();
        $presentCount = (clone $attQuery)->where('status', 'present')->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : null;

        $activeYear = AcademicYear::where('is_active', true)->first();

        return [
            'students' => $students,
            'teachers' => $teachers,
            'classes' => $classes,
            'attendanceRate' => $attendanceRate,
            'active_academic_year' => $activeYear?->only(['id', 'name']),
        ];
    }

    public function getAttendanceChartData(?int $academicYearId = null, ?int $days = 7)
    {
        $labels = [];
        $present = [];
        $absent = [];
        $sick = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');

            $query = AttendanceRecord::whereDate('date', $date);
            if ($academicYearId) $query->where('academic_year_id', $academicYearId);

            $present[] = (clone $query)->where('status', 'present')->count();
            $absent[] = (clone $query)->where('status', 'absent')->count();
            $sick[] = (clone $query)->whereIn('status', ['sick', 'leave'])->count();
        }

        return compact('labels', 'present', 'absent', 'sick');
    }

    public function getPerformanceChartData(?int $academicYearId = null)
    {
        $labels = [];
        $data = [];

        $query = Grade::select('subject_id', DB::raw('ROUND(AVG(score), 1) as avg_score'))
            ->whereNotNull('score');
        if ($academicYearId) $query->where('academic_year_id', $academicYearId);

        $averages = $query->groupBy('subject_id')
            ->with('subject:id,name')
            ->get();

        foreach ($averages as $row) {
            $labels[] = $row->subject?->name ?? "Subject #{$row->subject_id}";
            $data[] = (float) $row->avg_score;
        }

        return compact('labels', 'data');
    }

    public function getStudentPerformanceTrend(int $studentId, ?int $academicYearId = null)
    {
        $query = Grade::where('student_id', $studentId)->whereNotNull('score')->with('subject');
        if ($academicYearId) $query->where('academic_year_id', $academicYearId);

        $grades = $query->get();
        $byTerm = $grades->groupBy('term');

        $labels = [];
        $datasets = [];

        $subjects = $grades->pluck('subject.name')->unique();
        foreach ($subjects as $subject) {
            $datasets[$subject] = [];
        }

        foreach ($byTerm as $term => $termGrades) {
            $labels[] = $term;
            foreach ($subjects as $subject) {
                $found = $termGrades->firstWhere('subject.name', $subject);
                $datasets[$subject][] = $found ? (float) $found->score : null;
            }
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }
}
