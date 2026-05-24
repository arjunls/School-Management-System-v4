<?php

namespace App\Modules\HR\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\HR\Models\TeacherDetail;
use App\Modules\HR\Models\TeacherAttendance;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\PerformanceEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HRWebController extends Controller
{
    public function index()
    {
        $totalGuru = User::where('role', 'guru')->count();
        $hadirHariIni = TeacherAttendance::whereDate('date', today())->where('status', 'present')->count();
        $cutiPending = LeaveRequest::where('status', 'pending')->count();
        $totalIzin = LeaveRequest::whereMonth('created_at', now()->month)->count();
        $rataSkor = PerformanceEvaluation::whereMonth('evaluation_date', now()->month)->avg('score');
        $recentEvaluations = PerformanceEvaluation::with(['teacher', 'evaluator'])
            ->latest()->take(5)->get();
        $guruTerbaru = User::where('role', 'guru')->latest()->take(5)->get();

        return view('hr.index', compact(
            'totalGuru', 'hadirHariIni', 'cutiPending', 'totalIzin',
            'rataSkor', 'recentEvaluations', 'guruTerbaru'
        ));
    }

    public function detail(User $user)
    {
        if ($user->role !== 'guru') abort(404);
        $detail = TeacherDetail::where('user_id', $user->id)->first();
        $attendances = TeacherAttendance::where('user_id', $user->id)
            ->latest('date')->take(30)->get();
        $leaves = LeaveRequest::where('user_id', $user->id)
            ->latest()->get();
        $evaluations = PerformanceEvaluation::where('teacher_id', $user->id)
            ->with('evaluator')->latest()->get();

        return view('hr.detail', compact('user', 'detail', 'attendances', 'leaves', 'evaluations'));
    }

    public function attendance(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $attendances = TeacherAttendance::with('user')
            ->whereDate('date', $date)
            ->orderBy('check_in')
            ->paginate(25);
        $totalHadir = TeacherAttendance::whereDate('date', $date)->where('status', 'present')->count();
        $totalAbsen = TeacherAttendance::whereDate('date', $date)->where('status', 'absent')->count();
        $totalIjin = TeacherAttendance::whereDate('date', $date)->whereIn('status', ['sick', 'permit'])->count();

        return view('hr.attendance', compact('attendances', 'date', 'totalHadir', 'totalAbsen', 'totalIjin'));
    }

    public function checkIn(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = TeacherAttendance::where('user_id', $data['user_id'])
            ->whereDate('date', today())->first();

        if ($existing) {
            return back()->with('error', 'Guru sudah melakukan check-in hari ini.');
        }

        TeacherAttendance::create([
            'user_id' => $data['user_id'],
            'date' => today(),
            'check_in' => now(),
            'status' => 'present',
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkOut(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $attendance = TeacherAttendance::where('user_id', $data['user_id'])
            ->whereDate('date', today())->first();

        if (!$attendance) {
            return back()->with('error', 'Belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Sudah melakukan check-out hari ini.');
        }

        $attendance->update(['check_out' => now()]);

        return back()->with('success', 'Check-out berhasil.');
    }

    public function leave(Request $request)
    {
        $leaves = LeaveRequest::with(['user', 'approver'])
            ->latest()->paginate(25);
        $guru = User::where('role', 'guru')->orderBy('name')->get();

        if ($request->has('status')) {
            $leaves = LeaveRequest::with(['user', 'approver'])
                ->where('status', $request->status)
                ->latest()->paginate(25);
        }

        return view('hr.leave', compact('leaves', 'guru'));
    }

    public function storeLeave(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:sick,vacation,personal,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:2000',
        ]);

        $data['status'] = 'pending';
        LeaveRequest::create($data);

        return redirect()->route('hr.leave')->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    public function approveLeave(LeaveRequest $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Cuti sudah diproses.');
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Cuti berhasil disetujui.');
    }

    public function rejectLeave(Request $request, LeaveRequest $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Cuti sudah diproses.');
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Cuti berhasil ditolak.');
    }

    public function performance(Request $request)
    {
        $evaluations = PerformanceEvaluation::with(['teacher', 'evaluator'])
            ->latest()->paginate(25);
        $guru = User::where('role', 'guru')->orderBy('name')->get();

        if ($request->has('teacher_id')) {
            $evaluations = PerformanceEvaluation::with(['teacher', 'evaluator'])
                ->where('teacher_id', $request->teacher_id)
                ->latest()->paginate(25);
        }

        return view('hr.performance', compact('evaluations', 'guru'));
    }

    public function storePerformance(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'type' => 'required|in:monthly,quarterly,yearly',
            'evaluation_date' => 'required|date',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:5000',
        ]);

        $data['evaluator_id'] = auth()->id();
        PerformanceEvaluation::create($data);

        return redirect()->route('hr.performance')->with('success', 'Evaluasi berhasil disimpan.');
    }

    public function updatePerformance(Request $request, PerformanceEvaluation $evaluation)
    {
        $data = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:5000',
        ]);

        $evaluation->update($data);

        return redirect()->route('hr.performance')->with('success', 'Evaluasi berhasil diperbarui.');
    }

    public function destroyPerformance(PerformanceEvaluation $evaluation)
    {
        $evaluation->delete();
        return back()->with('success', 'Evaluasi berhasil dihapus.');
    }
}
