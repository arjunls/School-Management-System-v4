@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Mitra Industri</h1><a href="{{ route('industry.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800"><i class="fas fa-arrow-left mr-1"></i>Kembali</a></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Mitra Baru</h2>
        <form method="POST" action="{{ route('industry.partner.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Mitra <span class="text-red-500">*</span></label><input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label><textarea name="address" rows="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label><input type="text" name="phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input type="email" name="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">PIC</label><input type="text" name="pic_name" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Telepon PIC</label><input type="text" name="pic_phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kerjasama</label><select name="cooperation_type" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><option value="">-- Pilih --</option><option value="moa">MOA</option><option value="mou">MOU</option></select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="flex items-end"><button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-save mr-2"></i>Simpan</button></div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Daftar Mitra</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">PIC</th><th class="text-left px-4 py-3 font-medium">Telepon</th><th class="text-left px-4 py-3 font-medium">Kerjasama</th><th class="text-left px-4 py-3 font-medium">Program</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($partners as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->pic_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $p->phone ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="uppercase text-xs font-semibold">{{ $p->cooperation_type ?? '-' }}</span></td>
                        <td class="px-4 py-3">{{ $p->programs_count }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $p->status }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button onclick="editPartner({{ $p->id }}, '{{ addslashes($p->name) }}', '{{ addslashes($p->address) }}', '{{ $p->phone }}', '{{ $p->email }}', '{{ addslashes($p->pic_name) }}', '{{ $p->pic_phone }}', '{{ $p->cooperation_type }}', '{{ $p->start_date }}', '{{ $p->end_date }}', '{{ $p->status }}')" class="px-3 py-1 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('industry.partner.delete', $p) }}" onsubmit="return confirm('Hapus mitra ini?')">@csrf @method('DELETE')<button type="submit" class="px-3 py-1 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-slate-500 py-8">Belum ada mitra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">{{ $partners->links() }}</div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" onclick="if(event.target===this)closeEdit()">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-semibold text-slate-900">Edit Mitra</h3><button onclick="closeEdit()" class="p-1 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="" id="editForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf @method('PUT')
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Nama Mitra <span class="text-red-500">*</span></label><input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label><textarea name="address" id="edit_address" rows="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label><input type="text" name="phone" id="edit_phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input type="email" name="email" id="edit_email" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">PIC</label><input type="text" name="pic_name" id="edit_pic_name" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Telepon PIC</label><input type="text" name="pic_phone" id="edit_pic_phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kerjasama</label><select name="cooperation_type" id="edit_cooperation_type" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><option value="">-- Pilih --</option><option value="moa">MOA</option><option value="mou">MOU</option></select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" id="edit_start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" id="edit_end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" id="edit_status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="flex items-end"><button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-save mr-2"></i>Update</button></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editPartner(id, name, address, phone, email, picName, picPhone, cooperationType, startDate, endDate, status) {
    document.getElementById('editForm').action = '{{ route("industry.partner.update", "") }}/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_pic_name').value = picName;
    document.getElementById('edit_pic_phone').value = picPhone;
    document.getElementById('edit_cooperation_type').value = cooperationType;
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
