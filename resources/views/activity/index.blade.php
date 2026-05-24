@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Aktivitas Sistem</h1>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Deskripsi aktivitas..."
                x-on:input.debounce.500ms="$el.form.submit()"
                class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Jenis Log</label>
            <select name="log_name" x-on:change="$el.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                <option value="">Semua</option>
                @foreach($logNames as $name)
                <option value="{{ $name }}" @selected(request('log_name') === $name)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" x-on:change="$el.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" x-on:change="$el.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
        </div>
        @if(request()->anyFilled(['search', 'log_name', 'date_from', 'date_to']))
        <div class="flex items-end">
            <a href="{{ route('activity.index') }}" class="px-4 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Reset</a>
        </div>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($activities as $act)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $act->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $act->causer?->name ?? 'System' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($act->description === 'created') bg-green-100 text-green-800
                                @elseif($act->description === 'updated') bg-blue-100 text-blue-800
                                @elseif($act->description === 'deleted') bg-red-100 text-red-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $act->description }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-900 max-w-xs truncate">{{ $act->log_name }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">
                            @if($act->properties->count())
                                {{ json_encode($act->properties->toArray()) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada aktivitas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())<div class="px-6 py-4 border-t">{{ $activities->links() }}</div>@endif
    </div>
</div>
@endsection
