<?php

namespace App\Modules\StudentManagement\Student\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;

class SiswaPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $totalGrades = Grade::where('student_id', $user->id)->count();
        $totalHadir = AttendanceRecord::where('student_id', $user->id)->where('status', 'hadir')->count();
        $totalSakit = AttendanceRecord::where('student_id', $user->id)->where('status', 'sakit')->count();
        $totalIzin = AttendanceRecord::where('student_id', $user->id)->where('status', 'izin')->count();
        $totalAlpha = AttendanceRecord::where('student_id', $user->id)->where('status', 'alpha')->count();
        $totalTagihan = FeeInvoice::where('student_id', $user->id)->count();
        $totalLunas = FeeInvoice::where('student_id', $user->id)->where('status', 'paid')->count();

        $recentGrades = Grade::with('subject')->where('student_id', $user->id)->latest()->take(5)->get();
        $latestAttendance = AttendanceRecord::where('student_id', $user->id)->latest()->take(5)->get();

        return view('siswa.portal.dashboard', compact(
            'user', 'totalGrades', 'totalHadir', 'totalSakit', 'totalIzin', 'totalAlpha',
            'totalTagihan', 'totalLunas', 'recentGrades', 'latestAttendance'
        ));
    }

    public function grades()
    {
        $grades = Grade::with('subject')
            ->where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('siswa.portal.grades', compact('grades'));
    }

    public function attendance()
    {
        $records = AttendanceRecord::where('student_id', auth()->id())
            ->orderBy('date', 'desc')
            ->paginate(25);
        return view('siswa.portal.attendance', compact('records'));
    }

    public function schedule()
    {
        $user = auth()->user();
        $classId = $user->kelas_id;
        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu'];
        return view('siswa.portal.schedule', compact('schedules', 'days', 'user'));
    }

    public function payments()
    {
        $invoices = FeeInvoice::with('feeType')
            ->where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('siswa.portal.payments', compact('invoices'));
    }
}
