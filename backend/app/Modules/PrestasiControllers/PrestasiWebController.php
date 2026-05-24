<?php namespace App\Modules\PrestasiControllers;
use App\Kernel\Http\Controllers\Controller; use App\Models\Achievement; use App\Models\User; use Illuminate\Http\Request;
class PrestasiWebController extends Controller {
    public function index() { $achievements=Achievement::with('student')->orderBy('achievement_date','desc')->paginate(25); return view('prestasi.index',compact('achievements')); }
    public function store(Request $r) {
        $d=$r->validate(['student_id'=>'required|exists:users,id','title'=>'required','type'=>'nullable|in:academic,sport,art,religious,other','level'=>'nullable|in:school,district,province,national,international','rank'=>'nullable','description'=>'nullable','achievement_date'=>'required|date']);
        Achievement::create($d); return redirect()->route('prestasi.index')->with('success','Prestasi tercatat');
    }
    public function destroy(Achievement $prestasi) { $prestasi->delete(); return redirect()->route('prestasi.index')->with('success','Prestasi dihapus'); }
}
