<?php namespace App\Modules\PollingControllers;
use App\Kernel\Http\Controllers\Controller; use App\Models\Poll; use App\Models\PollOption; use App\Models\PollVote; use Illuminate\Http\Request;
class PollingWebController extends Controller {
    public function index() { $polls=Poll::with('options.votes')->orderBy('created_at','desc')->paginate(25); return view('polling.index',compact('polls')); }
    public function create() { return view('polling.form'); }
    public function store(Request $r) {
        $d=$r->validate(['title'=>'required','description'=>'nullable','start_at'=>'required|date','end_at'=>'required|date|after:start_at','options'=>'required|array|min:2','options.*'=>'required|string|max:255']);
        $poll=Poll::create(['title'=>$d['title'],'description'=>$d['description']??null,'start_at'=>$d['start_at'],'end_at'=>$d['end_at']]);
        foreach($d['options'] as $label) { PollOption::create(['poll_id'=>$poll->id,'label'=>$label]); }
        return redirect()->route('polling.index')->with('success','Polling berhasil dibuat');
    }
    public function vote(Request $r, Poll $polling) {
        $d=$r->validate(['option_id'=>'required|exists:poll_options,id']);
        $exists=PollVote::where('poll_id',$polling->id)->where('user_id',auth()->id())->exists();
        if($exists) return back()->with('error','Anda sudah voting');
        PollVote::create(['poll_id'=>$polling->id,'option_id'=>$d['option_id'],'user_id'=>auth()->id()]);
        PollOption::find($d['option_id'])->increment('votes');
        return redirect()->route('polling.index')->with('success','Voting berhasil');
    }
    public function destroy(Poll $polling) { $polling->options()->delete(); PollVote::where('poll_id',$polling->id)->delete(); $polling->delete(); return redirect()->route('polling.index')->with('success','Polling dihapus'); }
}
