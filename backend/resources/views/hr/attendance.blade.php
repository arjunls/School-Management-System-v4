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
        <h1 class="text-2xl font-bold text-slate-900">Absensi Guru</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('hr.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Hadir</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($totalHadir) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-check-circle text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Izin/Sakit</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalIjin) }}</p>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg"><i class="fas fa-clock text-yellow-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Alfa</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($totalAbsen) }}</p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg"><i class="fas fa-times-circle text-red-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}"
                class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Tampilkan</button>
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-4 border-b border-slate-200 flex flex-wrap gap-2">
            <button type="button" onclick="openCheckInModal()" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center gap-1">
                <i class="fas fa-sign-in-alt"></i>
                Check In
            </button>
            <button type="button" onclick="openCheckOutModal()" class="px-3 py-1.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm flex items-center gap-1">
                <i class="fas fa-sign-out-alt"></i>
                Check Out
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($attendances as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-medium">
                                    {{ substr($a->user->name, 0, 1) }}
                                </div>
                                <span>{{ $a->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->date->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->check_in?->format('H:i:s') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->check_out?->format('H:i:s') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $colors = ['present' => 'bg-green-100 text-green-800', 'absent' => 'bg-red-100 text-red-800', 'sick' => 'bg-yellow-100 text-yellow-800', 'permit' => 'bg-blue-100 text-blue-800'];
                                $labels = ['present' => 'Hadir', 'absent' => 'Alfa', 'sick' => 'Sakit', 'permit' => 'Izin'];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$a->status] ?? 'bg-slate-100' }}">
                                {{ $labels[$a->status] ?? $a->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $a->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada data absensi untuk tanggal ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<div id="checkInModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Check In Guru</h3>
        <form action="{{ route('hr.checkin') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Guru</label>
                <select name="user_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach(\App\Models\User::where('role', 'guru')->orderBy('name')->get() as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCheckInModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">Check In</button>
            </div>
        </form>
    </div>
</div>

<div id="checkOutModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Check Out Guru</h3>
        <form action="{{ route('hr.checkout') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Guru</label>
                <select name="user_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach(\App\Models\User::where('role', 'guru')->orderBy('name')->get() as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCheckOutModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">Check Out</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCheckInModal() { document.getElementById('checkInModal').classList.remove('hidden'); }
function closeCheckInModal() { document.getElementById('checkInModal').classList.add('hidden'); }
function openCheckOutModal() { document.getElementById('checkOutModal').classList.remove('hidden'); }
function closeCheckOutModal() { document.getElementById('checkOutModal').classList.add('hidden'); }
</script>
@endpush
