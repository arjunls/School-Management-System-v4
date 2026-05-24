@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">UKK / Sertifikasi BNSP</h1><div class="flex gap-2"><a href="{{ route('ukk.create.schema') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-layer-group mr-2"></i>Tambah Skema</a><a href="{{ route('ukk.create.cert') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-certificate mr-2"></i>Tambah Sertifikasi</a></div></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Skema Sertifikasi</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Bidang</th><th class="text-left px-4 py-3 font-medium">Level</th><th class="text-left px-4 py-3 font-medium">Sertifikat</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($schemas as $s)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $s->name }}</td>
                            <td class="px-4 py-3">{{ $s->field ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $s->level ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $s->certifications_count }}</span></td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('ukk.edit.schema', $s) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('ukk.destroy.schema', $s) }}" method="POST" onsubmit="return confirm('Hapus skema?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-slate-500 py-12">Belum ada skema</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Data Sertifikasi</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Skema</th><th class="text-left px-4 py-3 font-medium">Assesor</th><th class="text-left px-4 py-3 font-medium">Tanggal Uji</th><th class="text-left px-4 py-3 font-medium">Hasil</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($certifications as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $c->student->name }}</td>
                            <td class="px-4 py-3">{{ $c->schema->name }}</td>
                            <td class="px-4 py-3">{{ $c->assessor->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $c->exam_date ? \Carbon\Carbon::parse($c->exam_date)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($c->status == 'passed') bg-green-100 text-green-800
                                    @elseif($c->status == 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">{{ $c->result ?? $c->status ?? 'registered' }}</span>
                            </td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('ukk.edit.cert', $c) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('ukk.destroy.cert', $c) }}" method="POST" onsubmit="return confirm('Hapus sertifikasi?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada data sertifikasi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($certifications->hasPages())<div class="px-6 py-4">{{ $certifications->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
