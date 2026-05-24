@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Alumni</h1><a href="{{ route('alumni.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Alumni</a></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($alumni as $a)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">{{ substr($a->student->name ?? '?', 0, 1) }}</div>
                <div class="flex gap-2">
                    <a href="{{ route('alumni.edit', $a) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('alumni.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            <h3 class="font-bold text-slate-900">{{ $a->student->name ?? '-' }}</h3>
            <p class="text-xs text-slate-500">Lulus {{ $a->graduation_year ?? '-' }}</p>
            <div class="mt-3 space-y-1 text-xs text-slate-600">
                @if($a->current_occupation)<div><i class="fas fa-briefcase mr-1 w-4"></i>{{ $a->current_occupation }} @if($a->current_company) di {{ $a->current_company }}@endif</div>@endif
                @if($a->current_education)<div><i class="fas fa-graduation-cap mr-1 w-4"></i>{{ $a->current_education }}</div>@endif
                @if($a->phone)<div><i class="fas fa-phone mr-1 w-4"></i>{{ $a->phone }}</div>@endif
                @if($a->email)<div><i class="fas fa-envelope mr-1 w-4"></i>{{ $a->email }}</div>@endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada data alumni</div>
        @endforelse
    </div>
    @if($alumni->hasPages())<div class="px-6 py-4">{{ $alumni->links() }}</div>@endif
</div>
@endsection
