@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Program Industri</h1><a href="{{ route('industry.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800"><i class="fas fa-arrow-left mr-1"></i>Kembali</a></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Program Baru</h2>
        <form method="POST" action="{{ route('industry.program.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mitra <span class="text-red-500">*</span></label>
                <select name="partner_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Program <span class="text-red-500">*</span></label><input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Durasi (Bulan)</label><input type="number" name="duration_months" min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="flex items-end"><button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700"><i class="fas fa-save mr-2"></i>Simpan</button></div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Daftar Program</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Program</th><th class="text-left px-4 py-3 font-medium">Mitra</th><th class="text-left px-4 py-3 font-medium">Durasi</th><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($programs as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->partner->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $p->duration_months ? $p->duration_months . ' bln' : '-' }}</td>
                        <td class="px-4 py-3">{{ $p->students_count }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $p->status }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button onclick="editProgram({{ $p->id }}, {{ $p->partner_id }}, '{{ addslashes($p->name) }}', '{{ addslashes($p->description) }}', '{{ $p->duration_months }}', '{{ $p->start_date }}', '{{ $p->end_date }}', '{{ $p->status }}')" class="px-3 py-1 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('industry.program.delete', $p) }}" onsubmit="return confirm('Hapus program ini?')">@csrf @method('DELETE')<button type="submit" class="px-3 py-1 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada program</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">{{ $programs->links() }}</div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" onclick="if(event.target===this)closeEdit()">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-semibold text-slate-900">Edit Program</h3><button onclick="closeEdit()" class="p-1 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="" id="editForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mitra <span class="text-red-500">*</span></label>
                <select name="partner_id" id="edit_partner_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    @foreach($partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Program <span class="text-red-500">*</span></label><input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Durasi (Bulan)</label><input type="number" name="duration_months" id="edit_duration_months" min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" id="edit_description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" id="edit_start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" id="edit_end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" id="edit_status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="flex items-end"><button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700"><i class="fas fa-save mr-2"></i>Update</button></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editProgram(id, partnerId, name, description, durationMonths, startDate, endDate, status) {
    document.getElementById('editForm').action = '{{ route("industry.program.update", "") }}/' + id;
    document.getElementById('edit_partner_id').value = partnerId;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_duration_months').value = durationMonths;
    document.getElementById('edit_start_date').value = startDate;
    document.getElementById('edit_end_date').value = endDate;
    document.getElementById('edit_status').value = status;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}
function closeEdit() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
</script>
@endpush
@endsection
