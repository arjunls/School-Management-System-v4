@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('common.siswa') }}</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('siswa.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                {{ __('common.add_new') }}
            </a>
            <a href="{{ route('export.siswa') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                {{ __('common.export') }}
            </a>
            <form action="{{ route('import.siswa') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".csv" class="text-sm" required>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">{{ __('common.import') }}</button>
            </form>
        </div>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end" x-data="{ timer: null }">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NISN, email..."
                x-on:input.debounce.300ms="$el.form.submit()"
                class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Kelas</label>
            <select name="kelas_id" x-on:change="$el.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" x-on:change="$el.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                <option value="graduated" @selected(request('status') === 'graduated')>Lulus</option>
            </select>
        </div>
        @if(request()->anyFilled(['search', 'kelas_id', 'status']))
        <div class="flex items-end">
            <a href="{{ route('siswa.index') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50">Reset</a>
        </div>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.siswa') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.kelas') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.email') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.phone') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $s->nisn ?? $s->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm font-medium">
                                    {{ substr($s->name, 0, 1) }}
                                </div>
                                <span>{{ $s->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->kelas?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->email ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $s->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $statusColors = ['active' => 'bg-green-100 text-green-800', 'inactive' => 'bg-red-100 text-red-800', 'graduated' => 'bg-blue-100 text-blue-800']; @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$s->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ ucfirst($s->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            <a href="{{ route('qr.show', $s->id) }}" class="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded hover:bg-indigo-50" title="QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
                            <a href="{{ route('siswa.edit', $s) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("common.confirm_delete") }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">{{ __('common.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $siswa->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
