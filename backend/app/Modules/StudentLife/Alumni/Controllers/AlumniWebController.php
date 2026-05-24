<?php namespace App\Modules\StudentLife\Alumni\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentLife\Alumni\Models\Alumni;
use App\Models\User;
use Illuminate\Http\Request;

class AlumniWebController extends Controller {
    public function index() {
        $alumni = Alumni::with('student')->orderBy('graduation_year','desc')->paginate(25);
        return view('alumni.index', compact('alumni'));
    }
    public function create() {
        $students = User::where('role','student')->whereDoesntHave('alumni')->orderBy('name')->get();
        return view('alumni.form', compact('students'));
    }
    public function store(Request $r) {
        $d = $r->validate([
            'student_id'=>'required|exists:users,id|unique:alumni,student_id',
            'graduation_year'=>'nullable|string','final_status'=>'nullable|string',
            'current_occupation'=>'nullable|string','current_company'=>'nullable|string',
            'current_education'=>'nullable|string','phone'=>'nullable|string','email'=>'nullable|email','address'=>'nullable|string',
        ]);
        Alumni::create($d);
        return redirect()->route('alumni.index')->with('success','Data alumni tersimpan');
    }
    public function edit(Alumni $alumni) {
        return view('alumni.form', compact('alumni'));
    }
    public function update(Request $r, Alumni $alumni) {
        $d = $r->validate([
            'graduation_year'=>'nullable|string','final_status'=>'nullable|string',
            'current_occupation'=>'nullable|string','current_company'=>'nullable|string',
            'current_education'=>'nullable|string','phone'=>'nullable|string','email'=>'nullable|email','address'=>'nullable|string',
        ]);
        $d['is_tracing_data_updated'] = true;
        $alumni->update($d);
        return redirect()->route('alumni.index')->with('success','Data alumni diperbarui');
    }
    public function destroy(Alumni $alumni) {
        $alumni->delete();
        return redirect()->route('alumni.index')->with('success','Data alumni dihapus');
    }
}
