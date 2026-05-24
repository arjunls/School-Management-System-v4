<?php

namespace App\Modules\Academic\Schedule\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class JadwalWebController extends Controller
{
    public function index()
    {
        $jadwal = Schedule::with(['class', 'subject', 'teacher'])->orderBy('day_of_week')->orderBy('start_time')->paginate(25);
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalGrouped = collect($days)->mapWithKeys(fn($d) => [$d => Schedule::with(['class', 'subject', 'teacher'])->where('day_of_week', $d)->orderBy('start_time')->get()]);
        return view('jadwal.index', compact('jadwal', 'jadwalGrouped', 'days'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('jadwal.form', compact('kelas', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        Schedule::create($data);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit(Schedule $jadwal)
    {
        $kelas = Kelas::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('jadwal.form', compact('jadwal', 'kelas', 'subjects', 'teachers'));
    }

    public function update(Request $request, Schedule $jadwal)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        $jadwal->update($data);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(Schedule $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
