@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Rapor Digital</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('rapor.generate') }}" method="POST" class="space-y-4" target="_blank">
            @csrf
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Siswa</label>
                    <select name="student_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Siswa</option>
                        @foreach($siswa as $s)
                        <option value="{{ $s->id }}" @selected(request('student_id') == $s->id)>{{ $s->name }} ({{ $s->nisn ?? $s->id }} - {{ $s->kelas?->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Semester</label>
                    <select name="term_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Semester</option>
                        @foreach($tahunAjaran as $ta)
                        <optgroup label="{{ $ta->name }}">
                            @foreach($ta->terms as $t)
                            <option value="{{ $t->id }}" @selected(request('term_id') == $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <button type="submit" formaction="{{ route('rapor.preview') }}" class="px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Preview
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-900">Daftar Siswa</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $s->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $s->nisn ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $s->kelas?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">Belum ada siswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())<div class="px-6 py-4 border-t">{{ $siswa->links() }}</div>@endif
    </div>
</div>
@endsection
