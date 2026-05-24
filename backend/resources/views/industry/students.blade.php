@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Siswa Industri</h1><a href="{{ route('industry.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800"><i class="fas fa-arrow-left mr-1"></i>Kembali</a></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tugaskan Siswa ke Program</h2>
        <form method="POST" action="{{ route('industry.student.assign') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Program <span class="text-red-500">*</span></label>
                <select name="program_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}">{{ $prog->name }} - {{ $prog->partner->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa <span class="text-red-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($studentsList as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mentor</label>
                <select name="mentor_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mentor --</option>
                    @foreach($mentors as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></div>
            <div class="flex items-end"><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-user-plus mr-2"></i>Tugaskan</button></div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Daftar Siswa</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Program</th><th class="text-left px-4 py-3 font-medium">Mitra</th><th class="text-left px-4 py-3 font-medium">Mentor</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $st)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $st->student->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $st->program->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $st->program->partner->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $st->mentor->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $st->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($st->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">{{ $st->status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button onclick="editStudent({{ $st->id }}, '{{ $st->status }}', {{ $st->mentor_id ?? 'null' }}, '{{ $st->end_date }}')" class="px-3 py-1 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('industry.student.remove', $st) }}" onsubmit="return confirm('Hapus siswa dari program?')">@csrf @method('DELETE')<button type="submit" class="px-3 py-1 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada siswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">{{ $students->links() }}</div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" onclick="if(event.target===this)closeEdit()">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 w-full max-w-lg mx-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-semibold text-slate-900">Update Status Siswa</h3><button onclick="closeEdit()" class="p-1 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="" id="editForm" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="dropped">Dropped</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mentor</label>
                <select name="mentor_id" id="edit_mentor_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mentor --</option>
                    @foreach($mentors as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir</label><input type="date" name="end_date" id="edit_end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></div>
            <div><button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-save mr-2"></i>Update Status</button></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editStudent(id, status, mentorId, endDate) {
    document.getElementById('editForm').action = '{{ route("industry.student.update", "") }}/' + id;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_mentor_id').value = mentorId || '';
    document.getElementById('edit_end_date').value = endDate;
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
