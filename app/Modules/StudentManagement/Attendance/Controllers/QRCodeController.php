<?php

namespace App\Modules\StudentManagement\Attendance\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function show(string $studentId)
    {
        $student = User::where('role', 'siswa')->findOrFail($studentId);
        $qrData = route('qr.scan.process', ['studentId' => $student->id, 'token' => md5($student->id . config('app.key'))]);
        $qrSvg = QrCode::size(300)->generate($qrData);
        return view('kehadiran.qrcode', compact('student', 'qrSvg', 'qrData'));
    }

    public function processScan(Request $request, string $studentId, string $token)
    {
        if ($token !== md5($studentId . config('app.key'))) {
            abort(403, 'Token QR tidak valid');
        }

        $student = User::where('role', 'siswa')->findOrFail($studentId);

        $today = now()->toDateString();
        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Siswa sudah diabsen hari ini']);
            }
            return view('kehadiran.scan-result', ['status' => 'already', 'student' => $student, 'record' => $existing]);
        }

        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'hadir',
            'notes' => 'Absen via QR Code',
            'created_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Absensi berhasil', 'data' => $record]);
        }

        return view('kehadiran.scan-result', ['status' => 'success', 'student' => $student, 'record' => $record]);
    }

    public function scanner()
    {
        return view('kehadiran.scanner');
    }
}
