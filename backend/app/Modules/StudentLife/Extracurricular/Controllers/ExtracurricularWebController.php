<?php namespace App\Modules\StudentLife\Extracurricular\Controllers;
use App\Kernel\Http\Controllers\Controller; use App\Modules\StudentLife\Extracurricular\Models\Extracurricular; use App\Models\User; use Illuminate\Http\Request;
class ExtracurricularWebController extends Controller {
    public function index() { $ekskuls=Extracurricular::withCount('members')->orderBy('name')->paginate(25); return view('ekskul.index',compact('ekskuls')); }
    public function create() { return view('ekskul.form'); }
    public function store(Request $r) {
        $d=$r->validate(['name'=>'required','description'=>'nullable','coach'=>'nullable','schedule'=>'nullable','location'=>'nullable','max_participants'=>'nullable|integer','is_active'=>'nullable|boolean']);
        Extracurricular::create($d); return redirect()->route('ekskul.index')->with('success','Ekskul berhasil ditambahkan');
    }
    public function edit(Extracurricular $ekskul) { return view('ekskul.form',compact('ekskul')); }
    public function update(Request $r, Extracurricular $ekskul) {
        $d=$r->validate(['name'=>'required','description'=>'nullable','coach'=>'nullable','schedule'=>'nullable','location'=>'nullable','max_participants'=>'nullable|integer','is_active'=>'nullable|boolean']);
        $ekskul->update($d); return redirect()->route('ekskul.index')->with('success','Ekskul diperbarui');
    }
    public function destroy(Extracurricular $ekskul) { $ekskul->delete(); return redirect()->route('ekskul.index')->with('success','Ekskul dihapus'); }
}
