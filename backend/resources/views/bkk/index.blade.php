@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">BKK / DUDI</h1><div class="flex gap-2"><a href="{{ route('bkk.create.company') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-building mr-2"></i>Tambah Perusahaan</a><a href="{{ route('bkk.create.vacancy') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"><i class="fas fa-briefcase mr-2"></i>Tambah Lowongan</a></div></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Perusahaan Mitra</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Bidang</th><th class="text-left px-4 py-3 font-medium">Lowongan</th><th class="text-left px-4 py-3 font-medium">MoU</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($companies as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                            <td class="px-4 py-3">{{ $c->field ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $c->vacancies_count }}</span></td>
                            <td class="px-4 py-3">{{ $c->mou_date ? \Carbon\Carbon::parse($c->mou_date)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('bkk.edit.company', $c) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('bkk.destroy.company', $c) }}" method="POST" onsubmit="return confirm('Hapus perusahaan?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-slate-500 py-12">Belum ada perusahaan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($companies->hasPages())<div class="px-6 py-4">{{ $companies->links() }}</div>@endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Lowongan Kerja</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Judul</th><th class="text-left px-4 py-3 font-medium">Perusahaan</th><th class="text-left px-4 py-3 font-medium">Slots</th><th class="text-left px-4 py-3 font-medium">Tutup</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vacancies as $v)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium"><a href="{{ route('bkk.applications', $v) }}" class="text-indigo-600 hover:underline">{{ $v->title }}</a></td>
                            <td class="px-4 py-3">{{ $v->company->name }}</td>
                            <td class="px-4 py-3">{{ $v->slots ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $v->closing_date ? \Carbon\Carbon::parse($v->closing_date)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('bkk.edit.vacancy', $v) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('bkk.destroy.vacancy', $v) }}" method="POST" onsubmit="return confirm('Hapus lowongan?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-slate-500 py-12">Belum ada lowongan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($vacancies->hasPages())<div class="px-6 py-4">{{ $vacancies->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
