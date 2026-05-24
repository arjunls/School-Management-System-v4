<?php

namespace App\Modules\Asrama\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Asrama\Models\Dormitory;
use App\Modules\Asrama\Models\DormitoryRoom;
use App\Modules\Asrama\Models\DormitoryAssignment;
use Illuminate\Http\Request;

class AsramaWebController extends Controller
{
    public function index()
    {
        $dormitoryCount = Dormitory::count();
        $roomCount = DormitoryRoom::count();
        $activeAssignments = DormitoryAssignment::where('status', 'active')->count();
        $dormitories = Dormitory::withCount('rooms')->with('supervisor')->orderBy('name')->get();
        return view('asrama.index', compact(
            'dormitoryCount', 'roomCount', 'activeAssignments', 'dormitories'
        ));
    }

    public function storeDormitory(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,mixed',
            'capacity' => 'required|integer|min:1',
            'supervisor_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string',
        ]);
        Dormitory::create($d);
        return redirect()->route('asrama.index')->with('success', 'Asrama tersimpan');
    }

    public function updateDormitory(Request $r, Dormitory $dormitory)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,mixed',
            'capacity' => 'required|integer|min:1',
            'supervisor_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string',
        ]);
        $dormitory->update($d);
        return redirect()->route('asrama.index')->with('success', 'Asrama diperbarui');
    }

    public function deleteDormitory(Dormitory $dormitory)
    {
        $dormitory->delete();
        return redirect()->route('asrama.index')->with('success', 'Asrama dihapus');
    }

    public function storeRoom(Request $r)
    {
        $d = $r->validate([
            'dormitory_id' => 'required|exists:dormitories,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'floor' => 'nullable|integer|min:0',
        ]);
        DormitoryRoom::create($d);
        return redirect()->route('asrama.index')->with('success', 'Kamar tersimpan');
    }

    public function updateRoom(Request $r, DormitoryRoom $room)
    {
        $d = $r->validate([
            'dormitory_id' => 'required|exists:dormitories,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'floor' => 'nullable|integer|min:0',
        ]);
        $room->update($d);
        return redirect()->route('asrama.index')->with('success', 'Kamar diperbarui');
    }

    public function deleteRoom(DormitoryRoom $room)
    {
        $room->delete();
        return redirect()->route('asrama.index')->with('success', 'Kamar dihapus');
    }

    public function storeAssignment(Request $r)
    {
        $d = $r->validate([
            'room_id' => 'required|exists:dormitory_rooms,id',
            'student_id' => 'required|exists:users,id|unique:dormitory_assignments,student_id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'status' => 'required|in:active,checked_out',
            'notes' => 'nullable|string',
        ]);
        DormitoryAssignment::create($d);
        return redirect()->route('asrama.index')->with('success', 'Penempatan tersimpan');
    }

    public function updateAssignment(Request $r, DormitoryAssignment $assignment)
    {
        $d = $r->validate([
            'room_id' => 'required|exists:dormitory_rooms,id',
            'student_id' => 'required|exists:users,id|unique:dormitory_assignments,student_id,' . $assignment->id,
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'status' => 'required|in:active,checked_out',
            'notes' => 'nullable|string',
        ]);
        $assignment->update($d);
        return redirect()->route('asrama.index')->with('success', 'Penempatan diperbarui');
    }

    public function deleteAssignment(DormitoryAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('asrama.index')->with('success', 'Penempatan dihapus');
    }
}
