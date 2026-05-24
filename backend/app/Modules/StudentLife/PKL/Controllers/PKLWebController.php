<?php namespace App\Modules\StudentLife\PKL\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentLife\PKL\Models\PKLRecord;
use App\Models\User;
use Illuminate\Http\Request;

class PKLWebController extends Controller {
    public function index() {
        $records = PKLRecord::with('student')->orderBy('start_date','desc')->paginate(25);
        return view('pkl.index', compact('records'));
    }
    public function create() {
        $students = User::where('role','student')->orderBy('name')->get();
        return view('pkl.form', compact('students'));
    }
    public function store(Request $r) {
        $d = $r->validate([
            'student_id'=>'required|exists:users,id','company_name'=>'required|string','company_address'=>'nullable|string',
            'supervisor_name'=>'nullable|string','supervisor_phone'=>'nullable|string',
            'start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|in:active,completed,extended',
            'notes'=>'nullable|string',
        ]);
        PKLRecord::create($d);
        return redirect()->route('pkl.index')->with('success','Data PKL tersimpan');
    }
    public function edit(PKLRecord $pkl) {
        $students = User::where('role','student')->orderBy('name')->get();
        return view('pkl.form', compact('pkl','students'));
    }
    public function update(Request $r, PKLRecord $pkl) {
        $d = $r->validate([
            'student_id'=>'required|exists:users,id','company_name'=>'required|string','company_address'=>'nullable|string',
            'supervisor_name'=>'nullable|string','supervisor_phone'=>'nullable|string',
            'start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date','status'=>'nullable|in:active,completed,extended',
            'notes'=>'nullable|string',
        ]);
        $pkl->update($d);
        return redirect()->route('pkl.index')->with('success','Data PKL diperbarui');
    }
    public function destroy(PKLRecord $pkl) {
        $pkl->delete();
        return redirect()->route('pkl.index')->with('success','Data PKL dihapus');
    }
}
