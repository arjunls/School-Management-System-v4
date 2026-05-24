<?php namespace App\Modules\Learning\Assignment\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\Learning\Assignment\Models\Assignment;
use App\Modules\Learning\Assignment\Models\Submission;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class AssignmentWebController extends Controller {
    public function index() {
        $assignments = Assignment::with(['class', 'subject', 'teacher'])->orderBy('due_date', 'desc')->paginate(25);
        return view('tugas.index', compact('assignments'));
    }

    public function create() {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('tugas.form', compact('classes', 'subjects'));
    }

    public function store(Request $r) {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:10240',
        ]);
        $d['teacher_id'] = auth()->id();
        if ($r->hasFile('attachment')) {
            $d['attachment_path'] = $r->file('attachment')->store('assignments', 'public');
        }
        Assignment::create($d);
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dibuat');
    }

    public function edit(Assignment $tugas) {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('tugas.form', compact('tugas', 'classes', 'subjects'));
    }

    public function update(Request $r, Assignment $tugas) {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:10240',
        ]);
        if ($r->hasFile('attachment')) {
            $d['attachment_path'] = $r->file('attachment')->store('assignments', 'public');
        }
        $tugas->update($d);
        return redirect()->route('tugas.index')->with('success', 'Tugas diperbarui');
    }

    public function destroy(Assignment $tugas) {
        $tugas->delete();
        return redirect()->route('tugas.index')->with('success', 'Tugas dihapus');
    }

    public function submissions(Assignment $tugas) {
        $tugas->load(['submissions.student', 'class', 'subject']);
        return view('tugas.submissions', compact('tugas'));
    }

    public function grade(Request $r, Submission $pengumpulan) {
        $d = $r->validate(['score' => 'required|integer|min:0|max:' . $pengumpulan->assignment->max_score, 'feedback' => 'nullable|string']);
        $pengumpulan->update($d);
        return redirect()->back()->with('success', 'Nilai tersimpan');
    }
}
