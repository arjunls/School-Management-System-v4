@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Evaluasi Kinerja Guru</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Evaluasi
            </button>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Guru</label>
            <select name="teacher_id" onchange="this.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Guru</option>
                @foreach($guru as $g)
                <option value="{{ $g->id }}" @selected(request('teacher_id') == $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        @if(request('teacher_id'))
        <div class="flex items-end">
            <a href="{{ route('hr.performance') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50">Reset</a>
        </div>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Evaluator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Catatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($evaluations as $ev)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-medium">
                                    {{ substr($ev->teacher->name, 0, 1) }}
                                </div>
                                <span>{{ $ev->teacher->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ ucfirst($ev->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $ev->evaluation_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-lg {{ $ev->score >= 80 ? 'text-green-600' : ($ev->score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($ev->score) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $ev->evaluator->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-[200px] truncate">{{ $ev->notes ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            <button type="button" onclick="openEditModal({{ $ev->id }}, '{{ $ev->score }}', '{{ addslashes($ev->notes ?? '') }}')" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('hr.performance.destroy', $ev) }}" method="POST" class="inline" onsubmit="return confirm('Hapus evaluasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada data evaluasi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($evaluations->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $evaluations->links() }}
        </div>
        @endif
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Evaluasi Kinerja</h3>
        <form action="{{ route('hr.performance.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Guru</label>
                <select name="teacher_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe Evaluasi</label>
                <select name="type" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="monthly">Bulanan</option>
                    <option value="quarterly">Triwulan</option>
                    <option value="yearly">Tahunan</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Evaluasi</label>
                <input type="date" name="evaluation_date" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Skor (0-100)</label>
                <input type="number" name="score" min="0" max="100" step="0.1" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Edit Evaluasi Kinerja</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Skor (0-100)</label>
                <input type="number" name="score" id="editScore" min="0" max="100" step="0.1" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Catatan</label>
                <textarea name="notes" id="editNotes" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }
function openEditModal(id, score, notes) {
    document.getElementById('editForm').action = '/hr/performance/' + id;
    document.getElementById('editScore').value = score;
    document.getElementById('editNotes').value = notes;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endpush
