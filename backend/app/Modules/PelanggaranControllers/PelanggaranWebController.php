<?php namespace App\Modules\PelanggaranControllers;
use App\Kernel\Http\Controllers\Controller; use App\Models\Violation; use App\Models\StudentViolation; use App\Models\User; use Illuminate\Http\Request;
class PelanggaranWebController extends Controller {
    public function index() { $violations=Violation::orderBy('name')->paginate(15); $records=StudentViolation::with(['student','violation'])->orderBy('incident_date','desc')->paginate(15); return view('pelanggaran.index',compact('violations','records')); }
    public function storeViolation(Request $r) {
        $d=$r->validate(['name'=>'required','category'=>'required|in:ringan,sedang,berat','points'=>'nullable|integer','sanction'=>'nullable']);
        Violation::create($d); return redirect()->route('pelanggaran.index')->with('success','Jenis pelanggaran ditambahkan');
    }
    public function storeRecord(Request $r) {
        $d=$r->validate(['student_id'=>'required|exists:users,id','violation_id'=>'required|exists:violations,id','incident_date'=>'required|date','description'=>'nullable','action_taken'=>'nullable']);
        $d['recorded_by']=auth()->id(); StudentViolation::create($d);
        return redirect()->route('pelanggaran.index')->with('success','Pelanggaran tercatat');
    }
    public function destroyViolation(Violation $pelanggaran) { $pelanggaran->delete(); return redirect()->route('pelanggaran.index')->with('success','Dihapus'); }
    public function destroyRecord(StudentViolation $catatan) { $catatan->delete(); return redirect()->route('pelanggaran.index')->with('success','Catatan dihapus'); }
}
