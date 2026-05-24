@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Pesan Baru</h1>
        <a href="{{ route('messages.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('messages.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Subjek</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Penerima</label>
                <select name="participant_ids[]" multiple required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" size="6">
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(in_array($u->id, old('participant_ids', [])))>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Tekan Ctrl/Cmd untuk memilih lebih dari satu</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pesan</label>
                <textarea name="body" rows="5" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('body') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-paper-plane mr-2"></i>Kirim</button>
            </div>
        </form>
    </div>
</div>
@endsection
