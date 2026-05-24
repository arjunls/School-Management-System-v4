<?php

namespace App\Modules\Academic\Class\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use Illuminate\Http\Request;

class KelasWebController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['homeroomTeacher', 'students'])->orderBy('name')->paginate(25);
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $guru = User::where('role', 'guru')->orderBy('name')->get();
        return view('kelas.form', compact('guru'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:kelas,name',
            'grade_level' => 'nullable|string|max:20',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer|min:1|max:50',
        ]);

        Kelas::create($data);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit(Kelas $kelas)
    {
        $guru = User::where('role', 'guru')->orderBy('name')->get();
        return view('kelas.form', compact('kelas', 'guru'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:kelas,name,' . $kelas->id,
            'grade_level' => 'nullable|string|max:20',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer|min:1|max:50',
        ]);

        $kelas->update($data);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->students()->update(['kelas_id' => null]);
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load(['homeroomTeacher', 'students' => function ($q) {
            $q->orderBy('name');
        }]);
        return view('kelas.show', compact('kelas'));
    }
}
