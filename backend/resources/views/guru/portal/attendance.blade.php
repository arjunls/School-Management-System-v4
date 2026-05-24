@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('guru.portal.dashboard') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Absensi Siswa</h1>
            <p class="text-sm text-slate-500">{{ $kelas->name }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg" onchange="this.form.submit()">
        </div>
        <button type="button" onclick="document.getElementById('attendanceForm').classList.remove('hidden')" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Input Absensi</button>
    </form>

    <form id="attendanceForm" method="POST" action="{{ route('guru.portal.attendance.store', $kelas) }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 {{ $errors->any() ? '' : 'hidden' }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama Siswa</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($students as $i => $s)
                    @php $r = $records[$s->id] ?? null; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-sm text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-sm font-medium text-slate-900">{{ $s->name }}</td>
                        <td class="px-4 py-2 text-center">
                            <select name="attendance[{{ $s->id }}][status]" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                                @foreach(['hadir', 'sakit', 'izin', 'alpha'] as $st)
                                <option value="{{ $st }}" @selected(($r->status ?? '') === $st)>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="attendance[{{ $s->id }}][notes]" value="{{ $r->notes ?? '' }}" class="w-full px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-sm" placeholder="Catatan">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Simpan Kehadiran</button>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-3">Rekap Kehadiran {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
        @if($records->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($students as $s)
                    @php $r = $records[$s->id] ?? null; @endphp
                    @if($r)
                    <tr>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $s->name }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $r->status === 'hadir' ? 'bg-green-100 text-green-800' : ($r->status === 'sakit' ? 'bg-yellow-100 text-yellow-800' : ($r->status === 'izin' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">{{ ucfirst($r->status) }}</span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-slate-500 text-center py-4">Belum ada data kehadiran untuk tanggal ini</p>
        @endif
    </div>
</div>
@endsection
