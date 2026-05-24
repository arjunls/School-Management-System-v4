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
        <h1 class="text-2xl font-bold text-slate-900">Pengajuan Cuti</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Buat Pengajuan
            </button>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
            </select>
        </div>
        @if(request('status'))
        <div class="flex items-end">
            <a href="{{ route('hr.leave') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50">Reset</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Alasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Disetujui Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($leaves as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-medium">
                                    {{ substr($l->user->name, 0, 1) }}
                                </div>
                                <span>{{ $l->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ ucfirst($l->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $l->start_date->format('d M') }} - {{ $l->end_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-[200px] truncate">{{ $l->reason }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $leaveColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800']; @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leaveColors[$l->status] }}">
                                {{ ucfirst($l->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $l->approver->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            @if($l->status === 'pending')
                            <form action="{{ route('hr.leave.approve', $l) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('hr.leave.reject', $l) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-slate-400 px-2">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada pengajuan cuti</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $leaves->links() }}
        </div>
        @endif
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Buat Pengajuan Cuti</h3>
        <form action="{{ route('hr.leave.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Guru</label>
                <select name="user_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe Cuti</label>
                <select name="type" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="sick">Sakit</option>
                    <option value="vacation">Cuti Tahunan</option>
                    <option value="personal">Cuti Pribadi</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Alasan</label>
                <textarea name="reason" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
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
</script>
@endpush
