<?php namespace App\Modules\Learning\TeachingLog\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\Learning\TeachingLog\Models\DailyTeachingLog;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class JurnalWebController extends Controller {
    public function index() {
        $logs = DailyTeachingLog::with(['class', 'subject', 'teacher'])->orderBy('date', 'desc')->paginate(25);
        return view('jurnal.index', compact('logs'));
    }

    public function create() {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('jurnal.form', compact('classes', 'subjects'));
    }

    public function store(Request $r) {
        $d = $r->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'topic' => 'nullable|string',
            'material' => 'nullable|string',
            'notes' => 'nullable|string',
            'present_students' => 'nullable|integer|min:0',
            'absent_students' => 'nullable|integer|min:0',
        ]);
        $d['teacher_id'] = auth()->id();
        DailyTeachingLog::create($d);
        return redirect()->route('jurnal.index')->with('success', 'Jurnal tersimpan');
    }

    public function edit(DailyTeachingLog $jurnal) {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('jurnal.form', compact('jurnal', 'classes', 'subjects'));
    }

    public function update(Request $r, DailyTeachingLog $jurnal) {
        $d = $r->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'topic' => 'nullable|string',
            'material' => 'nullable|string',
            'notes' => 'nullable|string',
            'present_students' => 'nullable|integer|min:0',
            'absent_students' => 'nullable|integer|min:0',
        ]);
        $jurnal->update($d);
        return redirect()->route('jurnal.index')->with('success', 'Jurnal diperbarui');
    }

    public function destroy(DailyTeachingLog $jurnal) {
        $jurnal->delete();
        return redirect()->route('jurnal.index')->with('success', 'Jurnal dihapus');
    }
}
