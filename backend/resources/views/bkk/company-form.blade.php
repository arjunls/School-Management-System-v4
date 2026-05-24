@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('bkk.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($company) ? 'Edit Perusahaan' : 'Tambah Perusahaan' }}</h1></div>
    <form method="POST" action="{{ isset($company) ? route('bkk.update.company', $company) : route('bkk.store.company') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($company)) @method('PUT') @endif
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $company->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Bidang Usaha</label><input type="text" name="field" value="{{ old('field', $company->field ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label><textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('address', $company->address ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label><input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $company->email ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Website</label><input type="url" name="website" value="{{ old('website', $company->website ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Contact Person</label><input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal MoU</label><input type="date" name="mou_date" value="{{ old('mou_date', isset($company) && $company->mou_date ? $company->mou_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Masa Berlaku MoU</label><input type="date" name="mou_expiry" value="{{ old('mou_expiry', isset($company) && $company->mou_expiry ? $company->mou_expiry->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="active" {{ old('status', $company->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option><option value="inactive" {{ old('status', $company->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Nonaktif</option></select></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($company) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
