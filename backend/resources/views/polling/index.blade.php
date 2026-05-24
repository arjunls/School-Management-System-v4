@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Polling & Voting</h1><a href="{{ route('polling.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Polling</a></div>
    <div class="grid gap-6 sm:grid-cols-2">
        @forelse($polls as $p)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-4">
                <div><h3 class="font-bold text-slate-900 text-lg">{{ $p->title }}</h3><p class="text-sm text-slate-500">{{ Str::limit($p->description, 80) }}</p></div>
                <div class="flex gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->is_active && now()->between($p->start_at, $p->end_at) ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <form action="{{ route('polling.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            <div class="space-y-3 mb-4">
                @php $totalVotes = $p->options->sum('votes'); @endphp
                @foreach($p->options as $o)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1"><span class="text-slate-700">{{ $o->label }}</span><span class="text-slate-500 font-medium">{{ $totalVotes > 0 ? round(($o->votes / $totalVotes) * 100) : 0 }}% ({{ $o->votes }})</span></div>
                    <div class="w-full bg-slate-100 rounded-full h-2"><div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $totalVotes > 0 ? ($o->votes / $totalVotes) * 100 : 0 }}%"></div></div>
                </div>
                @endforeach
            </div>
            @php $userVoted = $p->options->pluck('id')->intersect($p->votes->where('user_id', auth()->id())->pluck('option_id'))->isNotEmpty(); @endphp
            @if(!$userVoted && $p->is_active && now()->between($p->start_at, $p->end_at))
            <form method="POST" action="{{ route('polling.vote', $p) }}" class="space-y-2">
                @csrf
                @foreach($p->options as $o)
                <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 border border-slate-200 cursor-pointer"><input type="radio" name="option_id" value="{{ $o->id }}" class="text-blue-600"><span class="text-sm text-slate-700">{{ $o->label }}</span></label>
                @endforeach
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors mt-3">Vote</button>
            </form>
            @elseif($userVoted)
            <p class="text-sm text-emerald-600 text-center"><i class="fas fa-check-circle mr-1"></i>Anda sudah voting</p>
            @else
            <p class="text-sm text-slate-400 text-center">Polling tidak aktif</p>
            @endif
            <p class="text-xs text-slate-400 mt-3 text-center">{{ \Carbon\Carbon::parse($p->start_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($p->end_at)->format('d M Y') }}</p>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada polling</div>
        @endforelse
    </div>
    @if($polls->hasPages())<div class="px-6 py-4">{{ $polls->links() }}</div>@endif
</div>
@endsection
