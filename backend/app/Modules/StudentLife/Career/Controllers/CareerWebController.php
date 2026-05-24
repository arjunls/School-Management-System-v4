<?php

namespace App\Modules\StudentLife\Career\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\StudentLife\Career\Models\CareerInterest;
use App\Modules\StudentLife\Career\Models\CareerPlan;
use Illuminate\Http\Request;

class CareerWebController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->with(['kelas', 'careerInterests', 'careerPlans'])
            ->orderBy('name')
            ->get();
        $kelasList = Kelas::orderBy('name')->get();
        return view('career.index', compact('students', 'kelasList'));
    }

    public function student(User $student)
    {
        $interests = CareerInterest::where('student_id', $student->id)->orderBy('score', 'desc')->get();
        $plans = CareerPlan::where('student_id', $student->id)->orderBy('created_at', 'desc')->get();
        return view('career.detail', compact('student', 'interests', 'plans'));
    }

    public function storeInterest(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'code' => 'required|string|max:10',
            'label' => 'required|string|max:100',
            'score' => 'required|integer|min:0|max:100',
            'test_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        CareerInterest::create($data);

        return redirect()->route('career.index')->with('success', 'Data minat bakat berhasil ditambahkan.');
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'plan_type' => 'required|in:study,work,entrepreneur',
            'institution' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'goal' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        CareerPlan::create($data);

        return redirect()->route('career.index')->with('success', 'Rencana karir berhasil ditambahkan.');
    }

    public function deleteInterest(CareerInterest $interest)
    {
        $interest->delete();
        return redirect()->route('career.index')->with('success', 'Data minat bakat berhasil dihapus.');
    }

    public function deletePlan(CareerPlan $plan)
    {
        $plan->delete();
        return redirect()->route('career.index')->with('success', 'Rencana karir berhasil dihapus.');
    }
}
