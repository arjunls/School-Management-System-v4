<?php

namespace App\Modules\Reporting\Report\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\Academic\AcademicYear\Models\AcademicYear;
use App\Modules\Academic\AcademicYear\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RaporWebController extends Controller
{
    public function index()
    {
        $siswa = User::where('role', 'siswa')
            ->with('kelas')
            ->orderBy('name')
            ->paginate(25);
        $tahunAjaran = AcademicYear::orderBy('name')->get();
        return view('rapor.index', compact('siswa', 'tahunAjaran'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $student = User::with('kelas')->findOrFail($data['student_id']);
        $term = Term::with('academicYear')->findOrFail($data['term_id']);

        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->orderBy('subject_id')
            ->get();

        $rataRata = $grades->avg('score');

        $pdf = Pdf::loadView('rapor.pdf', compact('student', 'term', 'grades', 'rataRata'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'rapor-' . $student->nisn . '-' . $term->name . '.pdf';
        return $pdf->download($filename);
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $student = User::with('kelas')->findOrFail($data['student_id']);
        $term = Term::with('academicYear')->findOrFail($data['term_id']);

        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->orderBy('subject_id')
            ->get();

        $rataRata = $grades->avg('score');

        return view('rapor.pdf', compact('student', 'term', 'grades', 'rataRata'));
    }
}
