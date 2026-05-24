<?php

namespace App\Modules\Dashboard\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;

class DashboardWebController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('siswa')) {
            return redirect()->route('siswa.portal.dashboard');
        }
        if ($user->hasRole('orang-tua')) {
            return redirect()->route('orangtua.portal.dashboard');
        }
        if ($user->hasRole('guru') || $user->hasRole('wali-kelas')) {
            return redirect()->route('guru.portal.dashboard');
        }

        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = Kelas::count();
        $todayAttendance = AttendanceRecord::whereDate('date', today())->count();
        $totalAttendance = AttendanceRecord::count();

        $attendanceRate = $totalAttendance > 0
            ? round((AttendanceRecord::where('status', 'hadir')->count() / $totalAttendance) * 100, 1)
            : 0;

        // Recent activity from activity log
        $recentActivities = \Spatie\Activitylog\Models\Activity::latest()->take(5)->get()->map(function ($a) {
            $icon = match (true) {
                str_contains($a->description, 'siswa') || str_contains($a->description, 'Siswa') => 'user-graduate',
                $a->description === 'created' => 'plus-circle',
                $a->description === 'updated' => 'edit',
                $a->description === 'deleted' => 'trash',
                default => 'info-circle',
            };
            $color = match ($a->description) {
                'created' => 'green',
                'updated' => 'blue',
                'deleted' => 'red',
                default => 'purple',
            };
            return [
                'icon' => $icon,
                'color' => $color,
                'text' => class_basename($a->subject_type) . ' ' . $a->description,
                'time' => $a->created_at->diffForHumans(),
            ];
        });

        // Monthly enrollment trend
        $months = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $enrollmentData = [];
        foreach (range(0, 11) as $i) {
            $enrollmentData[] = User::where('role', 'siswa')
                ->whereYear('created_at', now()->subMonths(11 - $i)->year)
                ->whereMonth('created_at', now()->subMonths(11 - $i)->month)
                ->count();
        }

        // Attendance distribution
        $attendanceDist = [
            AttendanceRecord::where('status', 'hadir')->count(),
            AttendanceRecord::where('status', 'sakit')->count(),
            AttendanceRecord::where('status', 'izin')->count(),
            AttendanceRecord::where('status', 'alpha')->count(),
        ];

        // Grade averages per subject
        $gradeLabels = [];
        $gradeData = [];
        $subjects = \App\Modules\Academic\Subject\Models\Subject::with('grades')->get();
        foreach ($subjects as $s) {
            $avg = $s->grades->avg('score');
            if ($avg) {
                $gradeLabels[] = $s->name;
                $gradeData[] = round($avg, 1);
            }
        }

        return view('dashboard.index', compact(
            'totalSiswa', 'totalGuru', 'totalKelas', 'attendanceRate',
            'todayAttendance', 'recentActivities',
            'months', 'enrollmentData', 'attendanceDist',
            'gradeLabels', 'gradeData'
        ));
    }
}
