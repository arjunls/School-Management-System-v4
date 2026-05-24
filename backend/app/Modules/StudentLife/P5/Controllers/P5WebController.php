<?php namespace App\Modules\StudentLife\P5\Controllers;
use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentLife\P5\Models\{P5Project,P5Activity};
use App\Modules\Academic\Class\Models\Kelas;
use Illuminate\Http\Request;

class P5WebController extends Controller {
    public function index() {
        $projects = P5Project::with(['class','coordinator','activities'])->orderBy('created_at','desc')->paginate(25);
        return view('p5.index', compact('projects'));
    }
    public function create() {
        $classes = Kelas::orderBy('name')->get();
        return view('p5.form', compact('classes'));
    }
    public function store(Request $r) {
        $d = $r->validate(['title'=>'required|string','description'=>'nullable','theme'=>'nullable','dimension'=>'nullable','class_id'=>'required|exists:kelas,id','start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date']);
        $d['coordinator_id'] = auth()->id();
        P5Project::create($d);
        return redirect()->route('p5.index')->with('success','Projek P5 berhasil dibuat');
    }
    public function edit(P5Project $p5) {
        $classes = Kelas::orderBy('name')->get();
        return view('p5.form', compact('p5','classes'));
    }
    public function update(Request $r, P5Project $p5) {
        $d = $r->validate(['title'=>'required|string','description'=>'nullable','theme'=>'nullable','dimension'=>'nullable','class_id'=>'required|exists:kelas,id','start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date']);
        $p5->update($d);
        return redirect()->route('p5.index')->with('success','Projek P5 diperbarui');
    }
    public function show(P5Project $p5) {
        $p5->load(['class','coordinator','activities']);
        return view('p5.show', compact('p5'));
    }
    public function storeActivity(Request $r, P5Project $p5) {
        $d = $r->validate(['name'=>'required|string','description'=>'nullable','date'=>'nullable|date','location'=>'nullable']);
        $p5->activities()->create($d);
        return redirect()->route('p5.show', $p5)->with('success','Kegiatan ditambahkan');
    }
    public function destroyActivity(P5Activity $kegiatan) {
        $kegiatan->delete();
        return redirect()->back()->with('success','Kegiatan dihapus');
    }
    public function destroy(P5Project $p5) {
        $p5->activities()->delete();
        $p5->delete();
        return redirect()->route('p5.index')->with('success','Projek P5 dihapus');
    }
}
