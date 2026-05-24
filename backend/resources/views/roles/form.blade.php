@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('roles.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($role) ? 'Edit Role: ' . $role->name : 'Buat Role Baru' }}</h1>
    </div>

    <form method="POST" action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="max-w-sm">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Role <span class="text-red-500">*</span></label>
            <input type="text" name="name" required value="{{ old('name', $role->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="contoh: kepala-sekolah">
        </div>

        <div>
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Permissions</h2>
            @foreach($permissions as $group => $perms)
            <div class="mb-4">
                <h3 class="text-sm font-medium text-slate-600 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2">{{ $group }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($perms as $perm)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 border border-slate-100 cursor-pointer transition-colors">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($perm->name, $rolePermissions ?? []) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">{{ $perm->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($role) ? 'Update Role' : 'Buat Role' }}</button>
            <a href="{{ route('roles.index') }}" class="px-6 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection
