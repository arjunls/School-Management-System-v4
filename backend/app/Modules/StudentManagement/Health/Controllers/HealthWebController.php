<?php namespace App\Modules\StudentManagement\Health\Controllers;
use App\Kernel\Http\Controllers\Controller; use App\Modules\StudentManagement\Health\Models\HealthRecord; use App\Models\User; use Illuminate\Http\Request;
class HealthWebController extends Controller {
    public function index() { $records=HealthRecord::with('student')->orderBy('check_date','desc')->paginate(25); return view('uks.index',compact('records')); }
    public function store(Request $r) {
        $d=$r->validate(['student_id'=>'required|exists:users,id','check_date'=>'required|date','complaint'=>'nullable','diagnosis'=>'nullable','action'=>'nullable','notes'=>'nullable']);
        HealthRecord::create($d); return redirect()->route('uks.index')->with('success','Rekam medis tersimpan');
    }
    public function destroy(HealthRecord $uk) { $uk->delete(); return redirect()->route('uks.index')->with('success','Data dihapus'); }
}
