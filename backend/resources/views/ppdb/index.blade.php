@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="text-center space-y-4">
        <h1 class="text-3xl font-bold text-slate-900">Penerimaan Peserta Didik Baru</h1>
        <p class="text-slate-600">Silakan pilih periode pendaftaran yang tersedia dan lengkapi formulir di bawah ini.</p>
    </div>

    @if($periods->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-6 py-8 text-center">
            <i class="fas fa-info-circle text-3xl mb-3"></i>
            <p class="text-lg font-medium">Belum ada periode pendaftaran yang dibuka.</p>
            <p class="text-sm mt-1">Silakan cek kembali di lain waktu.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($periods as $period)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $period->name }}</h3>
                        <p class="text-xs text-slate-500">Tahun Ajaran {{ $period->academic_year }}</p>
                    </div>
                </div>
                <div class="text-sm text-slate-600 space-y-1 mb-3">
                    <p><i class="fas fa-calendar-day w-5 text-slate-400"></i> {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</p>
                    <p><i class="fas fa-users w-5 text-slate-400"></i> Kuota: {{ $period->applicant_count }} / {{ $period->quota }}</p>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 mb-4">
                    @php $pct = $period->quota > 0 ? min(100, round(($period->applicant_count / $period->quota) * 100)) : 0; @endphp
                    <div class="bg-blue-600 rounded-full h-2 transition-all" style="width: {{ $pct }}%"></div>
                </div>
                <button type="button" onclick="selectPeriod('{{ $period->id }}', '{{ $period->name }}')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Daftar Sekarang
                </button>
            </div>
            @endforeach
        </div>

        <div id="registration-form" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hidden">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Formulir Pendaftaran</h2>
                <span id="selected-period-label" class="text-sm text-blue-600 font-medium"></span>
            </div>

            <form action="{{ route('ppdb.register') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="period_id" id="period_id" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('full_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nisn') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('birth_place') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('birth_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Agama</label>
                        <select name="religion" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $rel)
                            <option value="{{ $rel }}" @selected(old('religion') === $rel)>{{ $rel }}</option>
                            @endforeach
                        </select>
                        @error('religion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <textarea name="address" rows="3" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon/HP</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Sekolah Asal</label>
                        <input type="text" name="previous_school" value="{{ old('previous_school') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('previous_school') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-slate-900 pt-4 border-t border-slate-200">Data Orang Tua</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Ayah</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('father_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Ibu</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('mother_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon Orang Tua</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('parent_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Daftar
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@push('scripts')
<script>
function selectPeriod(id, name) {
    document.getElementById('period_id').value = id;
    document.getElementById('selected-period-label').textContent = 'Periode: ' + name;
    document.getElementById('registration-form').classList.remove('hidden');
    document.getElementById('registration-form').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush
@endsection
