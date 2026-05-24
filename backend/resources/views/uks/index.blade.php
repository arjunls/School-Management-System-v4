@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">UKS / Klinik</h1></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Catat Rekam Medis</h2>
        <form method="POST" action="{{ route('uks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <select name="student_id" required class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"><option value="">Pilih Siswa</option>@foreach(\App\Models\User::where('role','student')->get() as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }})</option>@endforeach</select>
            <input type="date" name="check_date" required class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <input type="text" name="complaint" placeholder="Keluhan" class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <input type="text" name="diagnosis" placeholder="Diagnosis" class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <input type="text" name="action" placeholder="Tindakan" class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-save mr-2"></i>Simpan</button>
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Riwayat Rekam Medis</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Keluhan</th><th class="text-left px-4 py-3 font-medium">Diagnosis</th><th class="text-left px-4 py-3 font-medium">Tindakan</th><th class="text-left px-4 py-3 font-medium"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $r->student->name }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($r->check_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $r->complaint }}</td>
                        <td class="px-4 py-3">{{ $r->diagnosis }}</td>
                        <td class="px-4 py-3">{{ $r->action }}</td>
                        <td class="px-4 py-3"><form action="{{ route('uks.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada rekam medis</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="px-6 py-4">{{ $records->links() }}</div>@endif
</div>
@endsection
