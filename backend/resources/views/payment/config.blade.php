@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Konfigurasi Payment Gateway</h1>
        <a href="{{ route('payment.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
    </div>

    @forelse($configs as $cfg)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900 capitalize">{{ $cfg->provider }}</h2>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cfg->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $cfg->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><dt class="text-xs text-slate-500">Merchant ID</dt><dd class="text-sm font-medium text-slate-900">{{ $cfg->merchant_id }}</dd></div>
            <div><dt class="text-xs text-slate-500">Server Key</dt><dd class="text-sm font-medium text-slate-900">••••••••</dd></div>
            <div><dt class="text-xs text-slate-500">Client Key</dt><dd class="text-sm font-medium text-slate-900">{{ $cfg->client_key }}</dd></div>
            <div><dt class="text-xs text-slate-500">Mode</dt><dd class="text-sm font-medium text-slate-900">{{ $cfg->is_production ? 'Production' : 'Sandbox' }}</dd></div>
        </dl>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center text-slate-500">Belum ada konfigurasi</div>
    @endforelse

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Tambah / Edit Konfigurasi</h2>
        <form method="POST" action="{{ route('payment.config.update') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Provider</label>
                    <select name="provider" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="midtrans">Midtrans</option>
                        <option value="doku">DOKU</option>
                        <option value="tripay">TriPay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Merchant ID</label>
                    <input type="text" name="merchant_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Server Key</label>
                    <input type="text" name="server_key" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Client Key</label>
                    <input type="text" name="client_key" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_production" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-slate-700">Production Mode</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-slate-700">Aktif</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
