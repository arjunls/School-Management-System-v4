<?php

namespace App\Modules\Printing\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\User;
use App\Modules\Academic\AcademicYear\Models\AcademicYear;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\Printing\Models\CertificateNumber;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IjazahController extends Controller
{
    public function ijazah(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'student_id' => 'required|exists:users,id',
                'academic_year_id' => 'required|exists:academic_years,id',
            ]);

            $student = User::with('kelas')->findOrFail($data['student_id']);
            $tahunAjaran = AcademicYear::findOrFail($data['academic_year_id']);
            $school = SchoolProfile::first();

            $grades = Grade::with('subject')
                ->where('student_id', $student->id)
                ->where('academic_year_id', $tahunAjaran->id)
                ->get();

            $rataNilai = $grades->avg('score');

            $cert = CertificateNumber::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'type' => 'ijazah',
                    'academic_year_id' => $tahunAjaran->id,
                ],
                [
                    'certificate_number' => $this->generateNumber('ijazah', $tahunAjaran),
                    'generated_at' => now(),
                    'generated_by' => auth()->id(),
                ]
            );

            $pdf = Pdf::loadView('printing.ijazah', compact(
                'student', 'tahunAjaran', 'school', 'grades', 'rataNilai', 'cert'
            ));
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('ijazah-' . $student->nisn . '.pdf');
        }

        $students = User::where('role', 'student')->orderBy('name')->get();
        $tahunAjaran = AcademicYear::orderBy('name', 'desc')->get();
        $classes = Kelas::orderBy('name')->get();

        return view('printing.ijazah-form', compact('students', 'tahunAjaran', 'classes'));
    }

    public function skhu(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'student_id' => 'required|exists:users,id',
                'academic_year_id' => 'required|exists:academic_years,id',
            ]);

            $student = User::with('kelas')->findOrFail($data['student_id']);
            $tahunAjaran = AcademicYear::findOrFail($data['academic_year_id']);
            $school = SchoolProfile::first();

            $grades = Grade::with('subject')
                ->where('student_id', $student->id)
                ->where('academic_year_id', $tahunAjaran->id)
                ->get();

            $rataNilai = $grades->avg('score');

            $cert = CertificateNumber::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'type' => 'skhu',
                    'academic_year_id' => $tahunAjaran->id,
                ],
                [
                    'certificate_number' => $this->generateNumber('skhu', $tahunAjaran),
                    'generated_at' => now(),
                    'generated_by' => auth()->id(),
                ]
            );

            $pdf = Pdf::loadView('printing.skhu', compact(
                'student', 'tahunAjaran', 'school', 'grades', 'rataNilai', 'cert'
            ));
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('skhu-' . $student->nisn . '.pdf');
        }

        $students = User::where('role', 'student')->orderBy('name')->get();
        $tahunAjaran = AcademicYear::orderBy('name', 'desc')->get();
        $classes = Kelas::orderBy('name')->get();

        return view('printing.skhu-form', compact('students', 'tahunAjaran', 'classes'));
    }

    private function generateNumber(string $type, AcademicYear $tahun): string
    {
        $prefix = strtoupper($type === 'ijazah' ? 'DN' : 'SKH');
        $count = CertificateNumber::where('type', $type)
            ->where('academic_year_id', $tahun->id)
            ->count() + 1;

        $year = now()->format('Y');
        return sprintf('%s-%02d/DS/%04d/%s', $prefix, $count, $year, $tahun->name);
    }
}
