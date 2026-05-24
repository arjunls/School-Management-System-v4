<?php

namespace App\Modules\Academic\Subject\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Academic\Subject\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectWebController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('teacher')->orderBy('name')->paginate(25);
        return view('mapel.index', compact('subjects'));
    }

    public function create()
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('mapel.form', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:20',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        Subject::create($data);
        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    public function edit(Subject $mapel)
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('mapel.form', compact('mapel', 'teachers'));
    }

    public function update(Request $request, Subject $mapel)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code,' . $mapel->id,
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:20',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $mapel->update($data);
        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function destroy(Subject $mapel)
    {
        $mapel->delete();
        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil dihapus');
    }
}
