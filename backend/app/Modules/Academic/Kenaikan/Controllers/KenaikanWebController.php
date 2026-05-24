<?php namespace App\Modules\Academic\Kenaikan\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Academic\Kenaikan\Models\ClassMove;
use App\Modules\Academic\Class\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KenaikanWebController extends Controller
{
    public function index()
    {
        $moves = ClassMove::with(['student', 'fromClass', 'toClass', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('kenaikan.index', compact('moves'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->where('status', 'active')->orderBy('name')->get();
        $classes = Kelas::orderBy('name')->get();
        return view('kenaikan.form', compact('students', 'classes'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'student_id' => 'required|exists:users,id',
            'from_class_id' => 'required|exists:kelas,id',
            'to_class_id' => 'required|exists:kelas,id|different:from_class_id',
            'academic_year' => 'nullable|string|max:50',
            'reason' => 'nullable|string',
            'is_graduated' => 'nullable|boolean',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
        $d['approved_by'] = auth()->id();
        $move = ClassMove::create($d);

        if (($d['is_graduated'] ?? false) || $r->has('update_class')) {
            User::where('id', $d['student_id'])->update(['kelas_id' => $d['to_class_id']]);
        }

        return redirect()->route('kenaikan.index')->with('success', 'Kenaikan kelas tersimpan');
    }

    public function process(Request $r)
    {
        $d = $r->validate([
            'from_class_id' => 'required|exists:kelas,id',
            'to_class_id' => 'required|exists:kelas,id|different:from_class_id',
            'academic_year' => 'nullable|string|max:50',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $count = 0;
        foreach ($d['student_ids'] as $studentId) {
            $exists = ClassMove::where('student_id', $studentId)
                ->where('academic_year', $d['academic_year'])
                ->where('from_class_id', $d['from_class_id'])
                ->exists();
            if ($exists) continue;

            ClassMove::create([
                'student_id' => $studentId,
                'from_class_id' => $d['from_class_id'],
                'to_class_id' => $d['to_class_id'],
                'academic_year' => $d['academic_year'],
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);
            User::where('id', $studentId)->update(['kelas_id' => $d['to_class_id']]);
            $count++;
        }

        return redirect()->route('kenaikan.index')
            ->with('success', "Kenaikan kelas batch selesai. {$count} siswa diproses.");
    }

    public function edit(ClassMove $move)
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        $classes = Kelas::orderBy('name')->get();
        return view('kenaikan.form', compact('move', 'students', 'classes'));
    }

    public function update(Request $r, ClassMove $move)
    {
        $d = $r->validate([
            'student_id' => 'required|exists:users,id',
            'from_class_id' => 'required|exists:kelas,id',
            'to_class_id' => 'required|exists:kelas,id|different:from_class_id',
            'academic_year' => 'nullable|string|max:50',
            'reason' => 'nullable|string',
            'is_graduated' => 'nullable|boolean',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
        $move->update($d);
        return redirect()->route('kenaikan.index')->with('success', 'Data kenaikan diperbarui');
    }

    public function destroy(ClassMove $move)
    {
        $move->delete();
        return redirect()->route('kenaikan.index')->with('success', 'Data kenaikan dihapus');
    }
}
