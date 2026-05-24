<?php namespace App\Modules\BkControllers;
use App\Kernel\Http\Controllers\Controller; use App\Models\CounselingRecord; use App\Models\User; use Illuminate\Http\Request;
class BkWebController extends Controller {
    public function index() { $records=CounselingRecord::with(['student','counselor'])->orderBy('session_date','desc')->paginate(25); return view('bk.index',compact('records')); }
    public function store(Request $r) {
        $d=$r->validate(['student_id'=>'required|exists:users,id','session_date'=>'required|date','category'=>'nullable','issue'=>'required','action'=>'nullable','notes'=>'nullable','is_confidential'=>'nullable|boolean']);
        $d['counselor_id']=auth()->id(); CounselingRecord::create($d);
        return redirect()->route('bk.index')->with('success','Catatan konseling tersimpan');
    }
    public function destroy(CounselingRecord $bk) { $bk->delete(); return redirect()->route('bk.index')->with('success','Catatan dihapus'); }
}
