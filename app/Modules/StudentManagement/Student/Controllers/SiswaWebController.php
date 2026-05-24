<?php

namespace App\Modules\StudentManagement\Student\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use Illuminate\Http\Request;

class SiswaWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')->with('kelas');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($kelasId = $request->get('kelas_id')) {
            $query->where('kelas_id', $kelasId);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $siswa = $query->orderBy('name')->paginate(25);
        $kelasList = Kelas::all();
        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::all();
        return view('siswa.form', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20|unique:users,nisn',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'tempat_lahir' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive,graduated',
        ]);

        $data['role'] = 'siswa';
        $data['password'] = bcrypt('password123');
        User::create($data);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit(User $siswa)
    {
        if ($siswa->role !== 'siswa') abort(404);
        $kelasList = Kelas::all();
        return view('siswa.form', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, User $siswa)
    {
        if ($siswa->role !== 'siswa') abort(404);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20|unique:users,nisn,' . $siswa->id,
            'email' => 'nullable|email|unique:users,email,' . $siswa->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'tempat_lahir' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive,graduated',
        ]);

        $siswa->update($data);
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy(User $siswa)
    {
        if ($siswa->role !== 'siswa') abort(404);
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus');
    }
}
