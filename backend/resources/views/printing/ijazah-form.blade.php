@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Cetak Ijazah</h1>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-2xl">
        <form method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Kelas (opsional)</label>
                <select id="class-filter" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Siswa <span class="text-red-500">*</span></label>
                <select name="student_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-class="{{ $student->kelas_id }}">{{ $student->name }} ({{ $student->nisn ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                <select name="academic_year_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <i class="fas fa-file-pdf mr-2"></i> Generate Ijazah PDF
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('class-filter').addEventListener('change', function() {
    const selected = this.value;
    document.querySelectorAll('select[name="student_id"] option').forEach(opt => {
        if (opt.value === '') return;
        if (!selected || opt.dataset.class === selected) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
    document.querySelector('select[name="student_id"]').value = '';
});
</script>
@endpush
@endsection
