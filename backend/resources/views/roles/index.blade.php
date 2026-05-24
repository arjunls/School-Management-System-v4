@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Manajemen Role & Permission</h1>
        <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Role Baru</a>
    </div>

    <div class="grid gap-4">
        @forelse($roles as $role)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $role->name === 'super-admin' ? 'bg-purple-100 text-purple-800' : ($role->name === 'admin' ? 'bg-blue-100 text-blue-800' : ($role->name === 'guru' || $role->name === 'wali-kelas' ? 'bg-green-100 text-green-800' : ($role->name === 'siswa' ? 'bg-amber-100 text-amber-800' : ($role->name === 'orang-tua' || $role->name === 'parent' ? 'bg-pink-100 text-pink-800' : 'bg-slate-100 text-slate-800')))) }}">{{ $role->name }}</span>
                    <span class="text-xs text-slate-400">{{ $role->permissions_count ?? $role->permissions->count() }} permissions</span>
                    <span class="text-xs text-slate-400">{{ $role->users_count ?? $role->users()->count() }} users</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('roles.edit', $role) }}" class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="fas fa-edit mr-1"></i>Edit</a>
                    @if(!in_array($role->name, ['super-admin','admin','siswa','guru','orang-tua']))
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Hapus role ini?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"><i class="fas fa-trash mr-1"></i>Hapus</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="p-4 flex flex-wrap gap-1.5">
                @foreach($role->permissions as $perm)
                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs font-medium">{{ $perm->name }}</span>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center text-slate-500 py-12">Belum ada role</div>
        @endforelse
    </div>
</div>
@endsection
