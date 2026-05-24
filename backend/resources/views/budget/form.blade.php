@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('budget.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($budget) ? 'Edit Anggaran' : 'Tambah Anggaran' }}</h1></div>
    <form method="POST" action="{{ isset($budget) ? route('budget.update', $budget) : route('budget.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($budget)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $budget->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Anggaran <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $budget->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $budget->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Anggaran (Planned) <span class="text-red-500">*</span></label><input type="number" name="planned_amount" required min="0" step="0.01" value="{{ old('planned_amount', $budget->planned_amount ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Realisasi</label><input type="number" name="realized_amount" min="0" step="0.01" value="{{ old('realized_amount', $budget->realized_amount ?? '0') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Periode</label><input type="text" name="period" value="{{ old('period', $budget->period ?? date('Y')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2" placeholder="2025"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="planned" {{ old('status', $budget->status ?? 'planned') == 'planned' ? 'selected' : '' }}>Direncanakan</option><option value="approved" {{ old('status', $budget->status ?? 'planned') == 'approved' ? 'selected' : '' }}>Disetujui</option><option value="realized" {{ old('status', $budget->status ?? 'planned') == 'realized' ? 'selected' : '' }}>Terealisasi</option></select></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($budget) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
