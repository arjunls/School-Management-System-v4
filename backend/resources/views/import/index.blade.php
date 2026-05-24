@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Import CSV</h1>

    @if(session('import_result'))
    @php $r = session('import_result'); @endphp
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3">
        <p class="font-medium">{{ $r['imported'] }} data berhasil diimport</p>
        @if(!empty($r['errors']))
        <ul class="mt-2 text-sm list-disc list-inside text-red-600">
            @foreach($r['errors'] as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        @endif
    </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['route' => 'import.siswa', 'title' => 'Siswa', 'icon' => 'user-graduate', 'fields' => 'nama, nisn, jenis_kelamin, email, telepon'],
            ['route' => 'import.guru', 'title' => 'Guru', 'icon' => 'chalkboard-teacher', 'fields' => 'nama, nip, email, telepon'],
            ['route' => 'import.kelas', 'title' => 'Kelas', 'icon' => 'chalkboard', 'fields' => 'nama, jurusan, tingkat'],
            ['route' => 'import.mapel', 'title' => 'Mata Pelajaran', 'icon' => 'book', 'fields' => 'nama, kode, jam'],
            ['route' => 'import.jadwal', 'title' => 'Jadwal', 'icon' => 'calendar-alt', 'fields' => 'kelas, mapel, hari, jam_mulai, jam_selesai, guru_id'],
            ['route' => 'import.nilai', 'title' => 'Nilai', 'icon' => 'list', 'fields' => 'nis, mata_pelajaran, nilai, semester, tahun_ajaran'],
            ['route' => 'import.kehadiran', 'title' => 'Kehadiran', 'icon' => 'check-square', 'fields' => 'nis, tanggal, status, keterangan'],
            ['route' => 'import.pembayaran', 'title' => 'Pembayaran', 'icon' => 'credit-card', 'fields' => 'nama_siswa, tagihan, jumlah, jatuh_tempo'],
        ] as $imp)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600"><i class="fas fa-{{ $imp['icon'] }}"></i></div>
                <h3 class="font-semibold text-slate-900">{{ $imp['title'] }}</h3>
            </div>
            <p class="text-xs text-slate-400 mb-3">Format header: <code class="bg-slate-100 px-1 rounded">{{ $imp['fields'] }}</code></p>
            <form method="POST" action="{{ route($imp['route']) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-sm text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border file:border-slate-300 file:bg-slate-50 file:text-xs file:text-slate-700 hover:file:bg-slate-100">
                <button type="submit" class="shrink-0 px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors"><i class="fas fa-upload"></i></button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
