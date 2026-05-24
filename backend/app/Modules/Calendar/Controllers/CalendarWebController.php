<?php namespace App\Modules\Calendar\Controllers;
use App\Kernel\Http\Controllers\Controller; use App\Modules\Calendar\Models\Event; use Illuminate\Http\Request;
class CalendarWebController extends Controller {
    public function index() { $events = Event::orderBy('start_date')->paginate(25); return view('kalender.index', compact('events')); }
    public function create() { return view('kalender.form'); }
    public function store(Request $r) {
        $d=$r->validate(['title'=>'required','description'=>'nullable','start_date'=>'required|date','end_date'=>'nullable|date','start_time'=>'nullable','end_time'=>'nullable','type'=>'nullable','color'=>'nullable']);
        $d['created_by']=auth()->id(); Event::create($d);
        return redirect()->route('kalender.index')->with('success','Event berhasil ditambahkan');
    }
    public function destroy(Event $kalender) { $kalender->delete(); return redirect()->route('kalender.index')->with('success','Event berhasil dihapus'); }
}
