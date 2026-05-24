<?php namespace App\Modules\Learning\ExamSchedule\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\Learning\ExamSchedule\Models\ExamSchedule;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class ExamScheduleWebController extends Controller {
    public function index() {
        $schedules = ExamSchedule::with(['class', 'subject', 'teacher'])->orderBy('exam_date', 'desc')->orderBy('start_time')->paginate(25);
        return view('jadwal-ujian.index', compact('schedules'));
    }

    public function create() {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('jadwal-ujian.form', compact('classes', 'subjects'));
    }

    public function store(Request $r) {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'nullable|string',
            'type' => 'required|in:midterm,final,quiz,other',
        ]);
        $d['teacher_id'] = auth()->id();
        ExamSchedule::create($d);
        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian dibuat');
    }

    public function edit(ExamSchedule $ujian) {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('jadwal-ujian.form', compact('ujian', 'classes', 'subjects'));
    }

    public function update(Request $r, ExamSchedule $ujian) {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'nullable|string',
            'type' => 'required|in:midterm,final,quiz,other',
        ]);
        $ujian->update($d);
        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian diperbarui');
    }

    public function destroy(ExamSchedule $ujian) {
        $ujian->delete();
        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian dihapus');
    }
}
