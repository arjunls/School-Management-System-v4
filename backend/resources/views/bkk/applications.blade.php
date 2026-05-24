@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center gap-3"><a href="{{ route('bkk.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><div><h1 class="text-2xl font-bold text-slate-900">{{ $vacancy->title }}</h1><p class="text-sm text-slate-500">{{ $vacancy->company->name }}</p></div></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Pelamar ({{ $vacancy->applications->count() }})</h2>
            <div x-data="{ open: false }">
                <button @click="open = !open" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-1"></i>Tambah Pelamar</button>
                <div x-show="open" @click.outside="open = false" class="mt-2 bg-white border border-slate-200 rounded-xl shadow-lg p-4 w-96">
                    <form method="POST" action="{{ route('bkk.apply', $vacancy) }}" class="space-y-3">
                        @csrf
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Pilih Siswa</label>
                            <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                <option value="">-- Pilih --</option>
                                @foreach(\App\Models\User::where('role','student')->where('status','active')->orderBy('name')->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">NISN</th><th class="text-left px-4 py-3 font-medium">Tanggal Lamar</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Catatan</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vacancy->applications as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $a->student->name }}</td>
                        <td class="px-4 py-3">{{ $a->student->nisn ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($a->status == 'applied') bg-gray-100 text-gray-800
                                @elseif($a->status == 'reviewed') bg-blue-100 text-blue-800
                                @elseif($a->status == 'interview') bg-amber-100 text-amber-800
                                @elseif($a->status == 'accepted') bg-green-100 text-green-800
                                @elseif($a->status == 'rejected') bg-red-100 text-red-800
                                @endif">{{ $a->status }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-[200px] truncate">{{ $a->notes ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('bkk.update.application.status', $a) }}" class="flex gap-1 items-center">
                                @csrf @method('PUT')
                                <select name="status" class="text-xs rounded border border-slate-300 px-2 py-1" onchange="this.form.submit()">
                                    <option value="applied" {{ $a->status == 'applied' ? 'selected' : '' }}>Applied</option>
                                    <option value="reviewed" {{ $a->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="interview" {{ $a->status == 'interview' ? 'selected' : '' }}>Interview</option>
                                    <option value="accepted" {{ $a->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ $a->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada pelamar</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
