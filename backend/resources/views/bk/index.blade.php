@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Bimbingan Konseling (BK)</h1></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Catat Konseling</h2>
        <form method="POST" action="{{ route('bk.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <select name="student_id" required class="rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Siswa</option>@foreach(\App\Models\User::where('role','student')->get() as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
                <input type="date" name="session_date" required class="rounded-lg border border-slate-300 px-4 py-2">
                <select name="category" class="rounded-lg border border-slate-300 px-4 py-2"><option value="">Kategori</option><option value="akademik">Akademik</option><option value="pribadi">Pribadi</option><option value="sosial">Sosial</option><option value="karir">Karir</option></select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <textarea name="issue" placeholder="Permasalahan" required rows="2" class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                <textarea name="action" placeholder="Tindakan" rows="2" class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex items-center gap-4">
                <textarea name="notes" placeholder="Catatan" rows="2" class="flex-1 rounded-lg border border-slate-300 px-4 py-2"></textarea>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_confidential" value="1" class="rounded border-slate-300"><span class="text-slate-700">Rahasia</span></label>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Riwayat Konseling</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Kategori</th><th class="text-left px-4 py-3 font-medium">Permasalahan</th><th class="text-left px-4 py-3 font-medium">Konselor</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($r->session_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $r->student->name }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $r->category ?? '-' }}</span></td>
                        <td class="px-4 py-3 max-w-xs truncate">{{ $r->issue }}</td>
                        <td class="px-4 py-3">{{ $r->counselor->name ?? '-' }}</td>
                        <td class="px-4 py-3"><form action="{{ route('bk.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada catatan konseling</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="px-6 py-4">{{ $records->links() }}</div>@endif
</div>
@endsection
