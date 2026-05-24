@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('common.kelas') }}</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('export.kelas') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                {{ __('common.export') }}
            </a>
            <a href="{{ route('kelas.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                {{ __('common.add_new') }}
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($kelas as $k)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $k->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $k->grade_level ?? '-' }}</p>
                    </div>
                </div>
                <span class="text-xs text-slate-500">{{ $k->students_count ?? $k->students->count() }} / {{ $k->capacity ?? '-' }} siswa</span>
            </div>
            <div class="text-sm text-slate-600 mb-4">
                <span class="font-medium">Wali Kelas:</span> {{ $k->homeroomTeacher?->name ?? '-' }}
            </div>
            <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                <a href="{{ route('kelas.show', $k) }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat</a>
                <a href="{{ route('kelas.edit', $k) }}" class="text-sm text-green-600 hover:text-green-800">Edit</a>
                <form action="{{ route('kelas.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("common.confirm_delete") }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">{{ __('common.no_data') }}</div>
        @endforelse
    </div>

    @if($kelas->hasPages())
    <div class="px-6 py-4">
        {{ $kelas->links() }}
    </div>
    @endif
</div>
@endsection
