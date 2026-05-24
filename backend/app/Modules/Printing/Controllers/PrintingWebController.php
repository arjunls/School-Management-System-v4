<?php

namespace App\Modules\Printing\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Learning\Grade\Models\Grade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrintingWebController extends Controller
{
    public function index()
    {
        return view('printing.index');
    }

    public function kartuPelajar(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'student_id' => 'required|exists:users,id',
            ]);

            $student = User::with('kelas')->findOrFail($data['student_id']);

            $pdf = Pdf::loadView('printing.kartu-pelajar', compact('student'));
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('kartu-pelajar-' . $student->nisn . '.pdf');
        }

        $students = User::where('role', 'student')->orderBy('name')->get();

        return view('printing.kartu-pelajar-form', compact('students'));
    }

    public function kwitansi(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'invoice_id' => 'required|exists:fee_invoices,id',
            ]);

            $invoice = FeeInvoice::with(['student', 'feeType', 'payments'])->findOrFail($data['invoice_id']);

            $pdf = Pdf::loadView('printing.kwitansi', compact('invoice'));
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('kwitansi-' . $invoice->id . '.pdf');
        }

        $invoices = FeeInvoice::with(['student', 'feeType'])->orderBy('created_at', 'desc')->get();

        return view('printing.kwitansi-form', compact('invoices'));
    }

    public function leggerNilai(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'class_id' => 'required|exists:kelas,id',
                'subject_id' => 'required|exists:subjects,id',
            ]);

            $kelas = Kelas::findOrFail($data['class_id']);
            $subject = Subject::findOrFail($data['subject_id']);
            $students = User::where('role', 'student')
                ->where('kelas_id', $kelas->id)
                ->orderBy('name')
                ->get();

            $grades = Grade::whereIn('student_id', $students->pluck('id'))
                ->where('subject_id', $subject->id)
                ->get()
                ->keyBy('student_id');

            $pdf = Pdf::loadView('printing.legger', compact('kelas', 'subject', 'students', 'grades'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('legger-nilai-' . $kelas->name . '-' . $subject->name . '.pdf');
        }

        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('printing.legger-form', compact('classes', 'subjects'));
    }
}
