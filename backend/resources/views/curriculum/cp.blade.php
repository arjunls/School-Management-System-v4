@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('curriculum.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">{{ $cp->code }} - {{ $cp->subject->name ?? '-' }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500">Fase:</span><p class="font-medium">Fase {{ $cp->phase }}</p></div>
            <div><span class="text-slate-500">Kelas:</span><p class="font-medium">{{ $cp->class ?? '-' }}</p></div>
        </div>
        <p class="text-sm text-slate-700 mt-3">{{ $cp->description }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @forelse($cp->tps as $tp)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $tp->code }}</h3>
                        <p class="text-sm text-slate-600">{{ $tp->description }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editTp({{ $tp->id }}, '{{ $tp->code }}', '{{ addslashes($tp->description) }}', {{ $tp->order }})" class="text-indigo-600 hover:text-indigo-800 text-sm"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('curriculum.tp.destroy', [$cp, $tp]) }}" method="POST" onsubmit="return confirm('Hapus TP?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>

                @if($tp->atps->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($tp->atps as $atp)
                    <div class="p-4 pl-8">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-900">ATP #{{ $atp->order }}</p>
                                <p class="text-sm text-slate-700 mt-1"><strong>Tujuan:</strong> {{ $atp->objective }}</p>
                                @if($atp->material)<p class="text-xs text-slate-500 mt-1"><strong>Materi:</strong> {{ $atp->material }}</p>@endif
                                @if($atp->assessment || $atp->method || $atp->hours)
                                <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500">
                                    @if($atp->assessment)<span><strong>Asesmen:</strong> {{ $atp->assessment }}</span>@endif
                                    @if($atp->method)<span><strong>Metode:</strong> {{ $atp->method }}</span>@endif
                                    @if($atp->hours)<span><strong>JP:</strong> {{ $atp->hours }} jam</span>@endif
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                <button onclick="editAtp({{ $atp->id }}, {{ $tp->id }}, '{{ addslashes($atp->objective) }}', '{{ addslashes($atp->material ?? '') }}', '{{ addslashes($atp->assessment ?? '') }}', '{{ addslashes($atp->method ?? '') }}', {{ $atp->hours ?? 0 }}, {{ $atp->order }})" class="text-indigo-600 hover:text-indigo-800 text-xs"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('curriculum.atp.destroy', [$cp, $tp, $atp]) }}" method="POST" onsubmit="return confirm('Hapus ATP?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="p-4 pl-8 bg-slate-50 border-t border-slate-200">
                    <button onclick="showAtpForm({{ $tp->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-plus mr-1"></i>Tambah ATP
                    </button>
                    <div id="atp-form-{{ $tp->id }}" class="hidden mt-3">
                        <form method="POST" action="{{ route('curriculum.atp.store', [$cp, $tp]) }}" class="space-y-3">
                            @csrf
                            <textarea name="objective" placeholder="Tujuan Pembelajaran" required rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"></textarea>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="material" placeholder="Materi" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
                                <input type="text" name="assessment" placeholder="Asesmen" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="method" placeholder="Metode" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
                                <input type="number" name="hours" placeholder="Jam Pelajaran" min="1" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs transition-colors">Simpan</button>
                                <button type="button" onclick="hideAtpForm({{ $tp->id }})" class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-xs transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center text-slate-500">
                Belum ada Tujuan Pembelajaran (TP)
            </div>
            @endforelse
        </div>

        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Tambah TP</h2>
                <form method="POST" action="{{ route('curriculum.tp.store', $cp) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="code" placeholder="Kode TP (misal: TP.1)" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <textarea name="description" placeholder="Deskripsi TP" required rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
                    <input type="number" name="order" placeholder="Urutan" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Tambah TP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="editTpModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50" style="display:none;">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Edit TP</h3>
        <form id="editTpForm" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <input type="text" name="code" id="editTpCode" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <textarea name="description" id="editTpDescription" required rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
            <input type="number" name="order" id="editTpOrder" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Simpan</button>
                <button type="button" onclick="closeEditTp()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>

<div id="editAtpModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50" style="display:none;">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Edit ATP</h3>
        <form id="editAtpForm" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <textarea name="objective" id="editAtpObjective" required rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
            <input type="text" name="material" id="editAtpMaterial" placeholder="Materi" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <input type="text" name="assessment" id="editAtpAssessment" placeholder="Asesmen" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <input type="text" name="method" id="editAtpMethod" placeholder="Metode" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <input type="number" name="hours" id="editAtpHours" placeholder="Jam Pelajaran" min="1" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Simpan</button>
                <button type="button" onclick="closeEditAtp()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editTp(id, code, description, order) {
    document.getElementById('editTpCode').value = code;
    document.getElementById('editTpDescription').value = description;
    document.getElementById('editTpOrder').value = order;
    document.getElementById('editTpForm').action = '{{ route("curriculum.tp.update", [$cp, ':id']) }}'.replace(':id', id);
    document.getElementById('editTpModal').style.display = 'flex';
}

function closeEditTp() {
    document.getElementById('editTpModal').style.display = 'none';
}

function showAtpForm(tpId) {
    document.getElementById('atp-form-' + tpId).classList.toggle('hidden');
}

function hideAtpForm(tpId) {
    document.getElementById('atp-form-' + tpId).classList.add('hidden');
}

function editAtp(id, tpId, objective, material, assessment, method, hours, order) {
    document.getElementById('editAtpObjective').value = objective;
    document.getElementById('editAtpMaterial').value = material;
    document.getElementById('editAtpAssessment').value = assessment;
    document.getElementById('editAtpMethod').value = method;
    document.getElementById('editAtpHours').value = hours;
    document.getElementById('editAtpForm').action = '{{ route("curriculum.atp.update", [$cp, ':tpId', ':id']) }}'.replace(':tpId', tpId).replace(':id', id);
    document.getElementById('editAtpModal').style.display = 'flex';
}

function closeEditAtp() {
    document.getElementById('editAtpModal').style.display = 'none';
}
</script>
@endpush
