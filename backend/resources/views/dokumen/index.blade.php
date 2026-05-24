@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('common.dokumen') }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Dokumen</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="akademik">Akademik</option>
                        <option value="administrasi">Administrasi</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">File (max 20MB)</label>
                    <input type="file" name="file" required class="w-full text-sm bg-slate-50 border border-slate-300 rounded-lg px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"><i class="fas fa-upload mr-2"></i>Unggah Dokumen</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-900">Daftar Dokumen</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Diunggah Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($documents as $doc)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-alt text-blue-500"></i>
                                <span class="font-medium">{{ $doc->title }}</span>
                            </div>
                            @if($doc->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $doc->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $doc->category ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            @if($doc->file_size > 1048576) {{ round($doc->file_size / 1048576, 1) }} MB
                            @elseif($doc->file_size > 1024) {{ round($doc->file_size / 1024, 0) }} KB
                            @else {{ $doc->file_size }} B @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $doc->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $doc->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('dokumen.download', $doc) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50"><i class="fas fa-download"></i></a>
                            <form action="{{ route('dokumen.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("common.confirm_delete") }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada dokumen</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())<div class="px-6 py-4 border-t border-slate-200">{{ $documents->links() }}</div>@endif
    </div>
</div>
@endsection
