<?php

namespace App\Modules\StaffManagement\Teacher\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GuruWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'guru');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $guru = $query->orderBy('name')->paginate(25);
        return view('guru.index', compact('guru'));
    }

    public function create()
    {
        return view('guru.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $data['role'] = 'guru';
        $data['password'] = bcrypt('password123');
        User::create($data);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan');
    }

    public function edit(User $guru)
    {
        if ($guru->role !== 'guru') abort(404);
        return view('guru.form', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        if ($guru->role !== 'guru') abort(404);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $guru->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $guru->update($data);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui');
    }

    public function destroy(User $guru)
    {
        if ($guru->role !== 'guru') abort(404);
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus');
    }
}
