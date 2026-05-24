<?php

namespace App\Modules\StudentManagement\Parent\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;

class OrangTuaPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $children = $user->children()->with('kelas')->get();

        $childStats = [];
        foreach ($children as $child) {
            $childStats[$child->id] = [
                'totalGrades' => Grade::where('student_id', $child->id)->count(),
                'totalHadir' => AttendanceRecord::where('student_id', $child->id)->where('status', 'hadir')->count(),
                'totalSakit' => AttendanceRecord::where('student_id', $child->id)->where('status', 'sakit')->count(),
                'totalIzin' => AttendanceRecord::where('student_id', $child->id)->where('status', 'izin')->count(),
                'totalAlpha' => AttendanceRecord::where('student_id', $child->id)->where('status', 'alpha')->count(),
                'totalTagihan' => FeeInvoice::where('student_id', $child->id)->count(),
                'totalLunas' => FeeInvoice::where('student_id', $child->id)->where('status', 'paid')->count(),
                'recentGrades' => Grade::with('subject')->where('student_id', $child->id)->latest()->take(3)->get(),
                'latestAttendance' => AttendanceRecord::where('student_id', $child->id)->latest()->take(3)->get(),
            ];
        }

        return view('orangtua.portal.dashboard', compact('user', 'children', 'childStats'));
    }

    public function grades($studentId)
    {
        $parent = auth()->user();
        $student = $parent->children()->where('student_id', $studentId)->with('kelas')->firstOrFail();
        $grades = Grade::with('subject')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('orangtua.portal.grades', compact('student', 'grades'));
    }

    public function attendance($studentId)
    {
        $parent = auth()->user();
        $student = $parent->children()->where('student_id', $studentId)->with('kelas')->firstOrFail();
        $records = AttendanceRecord::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->paginate(25);
        return view('orangtua.portal.attendance', compact('student', 'records'));
    }

    public function schedule($studentId)
    {
        $parent = auth()->user();
        $student = $parent->children()->where('student_id', $studentId)->with('kelas')->firstOrFail();
        $classId = $student->kelas_id;
        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        $days = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu'];
        return view('orangtua.portal.schedule', compact('student', 'schedules', 'days'));
    }

    public function payments($studentId)
    {
        $parent = auth()->user();
        $student = $parent->children()->where('student_id', $studentId)->with('kelas')->firstOrFail();
        $invoices = FeeInvoice::with('feeType')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('orangtua.portal.payments', compact('student', 'invoices'));
    }
}
