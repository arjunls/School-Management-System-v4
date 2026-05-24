@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('kelas.index') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">{{ $kelas->name }}</h1>
        </div>
        <a href="{{ route('kelas.edit', $kelas) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">Edit Kelas</a>
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Wali Kelas</p>
            <p class="text-lg font-semibold text-slate-900">{{ $kelas->homeroomTeacher?->name ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Tingkat</p>
            <p class="text-lg font-semibold text-slate-900">{{ $kelas->grade_level ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Jumlah Siswa</p>
            <p class="text-lg font-semibold text-slate-900">{{ $kelas->students->count() }} / {{ $kelas->capacity ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">Daftar Siswa</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($kelas->students as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->nisn ?? $s->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->gender === 'male' ? 'Laki-laki' : ($s->gender === 'female' ? 'Perempuan' : '-') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($s->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($s->status ?? 'active') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada siswa di kelas ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
