@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.index') }}" class="text-slate-500 hover:text-slate-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Profil Guru</h1>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold mx-auto mb-3">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
                @if($detail)
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">NIP</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->nip ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">NUPTK</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->nuptk ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Sertifikasi</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->certification ? 'Ya' : 'Tidak' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Pendidikan</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->education ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Institusi</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->education_institution ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Tahun Lulus</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->graduation_year ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Tempat/Tgl Lahir</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->birth_place ?? '-' }}, {{ $detail->birth_date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Agama</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->religion ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Status</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->marital_status ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Status Kepegawaian</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->employment_status ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Tgl Masuk</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->join_date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Spesialisasi</span>
                        <span class="text-sm font-medium text-slate-900">{{ $detail->subject_specialization ?? '-' }}</span>
                    </div>
                    @if($detail->address)
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-slate-500">Alamat</span>
                        <span class="text-sm font-medium text-slate-900 text-right max-w-[200px]">{{ $detail->address }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-slate-400 text-center">Detail guru belum lengkap</p>
                @endif
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Riwayat Absensi (30 Hari Terakhir)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->check_in?->format('H:i') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->check_out?->format('H:i') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = ['present' => 'bg-green-100 text-green-800', 'absent' => 'bg-red-100 text-red-800', 'sick' => 'bg-yellow-100 text-yellow-800', 'permit' => 'bg-blue-100 text-blue-800'];
                                        $statusLabels = ['present' => 'Hadir', 'absent' => 'Alfa', 'sick' => 'Sakit', 'permit' => 'Izin'];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$a->status] ?? 'bg-slate-100 text-slate-800' }}">
                                        {{ $statusLabels[$a->status] ?? $a->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $a->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data absensi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Riwayat Cuti</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Alasan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($leaves as $l)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ ucfirst($l->type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $l->start_date->format('d M') }} - {{ $l->end_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500 max-w-[200px] truncate">{{ $l->reason }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $leaveColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800']; @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $leaveColors[$l->status] ?? 'bg-slate-100' }}">
                                        {{ ucfirst($l->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada data cuti</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Riwayat Evaluasi Kinerja</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Skor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Evaluator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($evaluations as $ev)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ ucfirst($ev->type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $ev->evaluation_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold {{ $ev->score >= 80 ? 'text-green-600' : ($ev->score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($ev->score) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $ev->evaluator->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500 max-w-[200px] truncate">{{ $ev->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada evaluasi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
