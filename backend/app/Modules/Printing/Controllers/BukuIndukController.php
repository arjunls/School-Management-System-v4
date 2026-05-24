<?php

namespace App\Modules\Printing\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BukuIndukController extends Controller
{
    public function bukuInduk(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'class_id' => 'nullable|exists:kelas,id',
                'angkatan' => 'nullable|string|max:50',
            ]);

            $query = User::where('role', 'student')
                ->with('kelas')
                ->orderBy('name');

            if (!empty($data['class_id'])) {
                $query->where('kelas_id', $data['class_id']);
            }
            if (!empty($data['angkatan'])) {
                $query->where('nisn', 'like', $data['angkatan'] . '%');
            }

            $students = $query->get();
            $kelas = !empty($data['class_id']) ? Kelas::find($data['class_id']) : null;

            $pdf = Pdf::loadView('printing.buku-induk', compact('students', 'kelas'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('buku-induk' . ($kelas ? '-' . $kelas->name : '') . '.pdf');
        }

        $classes = Kelas::orderBy('name')->get();
        return view('printing.buku-induk-form', compact('classes'));
    }
}
