@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Pelanggaran & Tata Tertib</h1></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Jenis Pelanggaran</h2>
        <form method="POST" action="{{ route('pelanggaran.violation.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            @csrf
            <input type="text" name="name" placeholder="Nama Pelanggaran" required class="rounded-lg border border-slate-300 px-4 py-2">
            <select name="category" required class="rounded-lg border border-slate-300 px-4 py-2"><option value="ringan">Ringan</option><option value="sedang">Sedang</option><option value="berat">Berat</option></select>
            <input type="number" name="points" placeholder="Poin" class="rounded-lg border border-slate-300 px-4 py-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Jenis Pelanggaran</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse($violations as $v)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                    <div><p class="font-medium text-slate-900">{{ $v->name }}</p><p class="text-xs text-slate-500">{{ $v->category }} @if($v->points) &middot; {{ $v->points }} poin @endif</p></div>
                    <form action="{{ route('pelanggaran.violation.destroy', $v) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
                @empty
                <div class="text-center text-slate-500 py-8">Belum ada jenis pelanggaran</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">Catat Pelanggaran Siswa</h2>
            <form method="POST" action="{{ route('pelanggaran.record.store') }}" class="space-y-4">
                @csrf
                <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Siswa</option>@foreach(\App\Models\User::where('role','student')->get() as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
                <select name="violation_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Pelanggaran</option>@foreach($violations as $v)<option value="{{ $v->id }}">{{ $v->name }} ({{ $v->category }})</option>@endforeach</select>
                <input type="date" name="incident_date" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <textarea name="description" placeholder="Keterangan" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2"></textarea>
                <input type="text" name="action_taken" placeholder="Tindakan" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Catat</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Riwayat Pelanggaran</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Pelanggaran</th><th class="text-left px-4 py-3 font-medium">Kategori</th><th class="text-left px-4 py-3 font-medium">Tindakan</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($r->incident_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $r->student->name }}</td>
                        <td class="px-4 py-3">{{ $r->violation->name }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $r->violation->category === 'berat' ? 'bg-red-100 text-red-800' : ($r->violation->category === 'sedang' ? 'bg-amber-100 text-amber-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $r->violation->category }}</span></td>
                        <td class="px-4 py-3">{{ $r->action_taken ?? '-' }}</td>
                        <td class="px-4 py-3"><form action="{{ route('pelanggaran.record.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada pelanggaran tercatat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="px-6 py-4">{{ $records->links() }}</div>@endif
</div>
@endsection
