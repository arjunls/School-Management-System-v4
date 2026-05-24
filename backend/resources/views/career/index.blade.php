@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Bimbingan Karir</h1>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-brain text-purple-600 mr-2"></i> Tes Minat Bakat (RIASEC)
            </h2>
            <form method="POST" action="{{ route('career.interest.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Siswa</label>
                    <select name="student_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->kelas?->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Kode</label>
                        <select name="code" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="R">R - Realistic</option>
                            <option value="I">I - Investigative</option>
                            <option value="A">A - Artistic</option>
                            <option value="S">S - Social</option>
                            <option value="E">E - Enterprising</option>
                            <option value="C">C - Conventional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Skor (0-100)</label>
                        <input type="number" name="score" min="0" max="100" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Tes</label>
                        <input type="date" name="test_date" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan</label>
                    <input type="text" name="notes" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-road text-amber-600 mr-2"></i> Rencana Karir / Studi Lanjut
            </h2>
            <form method="POST" action="{{ route('career.plan.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Siswa</label>
                    <select name="student_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->kelas?->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Jenis Rencana</label>
                    <select name="plan_type" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="study">Studi Lanjut (Kuliah)</option>
                        <option value="work">Bekerja</option>
                        <option value="entrepreneur">Wirausaha</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Institusi / Perusahaan Tujuan</label>
                    <input type="text" name="institution" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Jurusan / Bidang</label>
                        <input type="text" name="major" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Tujuan</label>
                        <input type="text" name="goal" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Data Bimbingan Karir</h2>
            <div>
                <select id="student-filter" class="px-4 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200" id="career-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Minat Bakat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Rencana</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($students as $s)
                    @php
                        $interests = $s->careerInterests ?? collect();
                        $plans = $s->careerPlans ?? collect();
                    @endphp
                    <tr class="hover:bg-slate-50" data-student-id="{{ $s->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $s->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $s->kelas?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            @foreach($interests as $i)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 mr-1">
                                {{ $i->code }}: {{ $i->score }}
                            </span>
                            @endforeach
                            @if($interests->isEmpty()) - @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            @foreach($plans as $p)
                            <div class="text-xs mb-1">
                                <span class="inline-block px-2 py-0.5 rounded font-medium
                                    {{ $p->plan_type === 'study' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $p->plan_type === 'work' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $p->plan_type === 'entrepreneur' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ $p->plan_type === 'study' ? 'Kuliah' : ($p->plan_type === 'work' ? 'Kerja' : 'Wirausaha') }}
                                </span>
                                {{ $p->institution ?? '' }}
                            </div>
                            @endforeach
                            @if($plans->isEmpty()) - @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <a href="{{ route('career.student', $s->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada siswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('student-filter')?.addEventListener('change', function() {
    const selected = this.value;
    document.querySelectorAll('#career-table tbody tr').forEach(row => {
        if (!selected || row.dataset.studentId === selected) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection
