<?php

namespace App\Modules\StaffManagement\User\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserWebController extends Controller
{
    public function index()
    {
        $users = User::with('kelas', 'roles')->orderBy('name')->paginate(25);
        return view('pengguna.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all()->pluck('name');
        $kelasList = Kelas::orderBy('name')->get();
        return view('pengguna.form', compact('roles', 'kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,teacher,student,parent,staff',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nisn' => 'nullable|string|max:20|unique:users,nisn',
            'jurusan' => 'nullable|string|max:50',
            'spatie_role' => 'required|string|exists:roles,name',
        ]);

        $data['password'] = bcrypt($data['password']);
        $spatieRole = $data['spatie_role'];
        unset($data['spatie_role']);

        $user = User::create($data);
        $user->assignRole($spatieRole);

        activity()->performedOn($user)->log('created');

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $pengguna)
    {
        $roles = Role::all()->pluck('name');
        $kelasList = Kelas::orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $parentStudents = $pengguna->children()->pluck('student_id')->toArray();
        return view('pengguna.form', compact('pengguna', 'roles', 'kelasList', 'students', 'parentStudents'));
    }

    public function update(Request $request, User $pengguna)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pengguna->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,teacher,student,parent,staff',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nisn' => 'nullable|string|max:20|unique:users,nisn,' . $pengguna->id,
            'jurusan' => 'nullable|string|max:50',
            'spatie_role' => 'required|string|exists:roles,name',
            'children' => 'nullable|array',
            'children.*' => 'exists:users,id',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $spatieRole = $data['spatie_role'];
        unset($data['spatie_role']);

        $children = $data['children'] ?? [];
        unset($data['children']);

        $pengguna->update($data);
        $pengguna->syncRoles([$spatieRole]);

        if ($pengguna->role === 'parent') {
            $pengguna->children()->sync($children);
        }

        activity()->performedOn($pengguna)->log('updated');

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $pengguna)
    {
        $pengguna->delete();
        activity()->performedOn($pengguna)->log('deleted');
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
