<?php

namespace App\Modules\Reporting\Report\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Http\Request;

class LaporanWebController extends Controller
{
    public function index()
    {
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalGrades = Grade::count();
        $totalAttendance = AttendanceRecord::count();
        $rekapKehadiran = AttendanceRecord::selectRaw("status, count(*) as total")->groupBy('status')->get()->pluck('total', 'status');
        return view('laporan.index', compact('totalSiswa', 'totalGuru', 'totalGrades', 'totalAttendance', 'rekapKehadiran'));
    }

    public function attendance(Request $request)
    {
        $query = AttendanceRecord::with('student');
        if ($request->date_from) $query->whereDate('date', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('date', '<=', $request->date_to);
        if ($request->status) $query->where('status', $request->status);

        $records = $query->orderBy('date', 'desc')->paginate(50);
        return view('laporan.attendance', compact('records'));
    }

    public function grades(Request $request)
    {
        $query = Grade::with(['student', 'subject']);
        if ($request->student_id) $query->where('student_id', $request->student_id);
        if ($request->subject_id) $query->where('subject_id', $request->subject_id);

        $grades = $query->orderBy('created_at', 'desc')->paginate(50);
        $siswa = User::where('role', 'siswa')->orderBy('name')->get();
        $subjects = \App\Modules\Academic\Subject\Models\Subject::all();
        return view('laporan.grades', compact('grades', 'siswa', 'subjects'));
    }

    public function payments()
    {
        $invoices = \App\Modules\Finance\Fee\Models\FeeInvoice::with(['student', 'feeType', 'payments'])
            ->orderBy('created_at', 'desc')->paginate(50);
        return view('laporan.payments', compact('invoices'));
    }
}
