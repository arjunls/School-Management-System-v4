<?php namespace App\Modules\Industry\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Industry\Models\IndustryPartner;
use App\Modules\Industry\Models\IndustryProgram;
use App\Modules\Industry\Models\IndustryStudent;
use Illuminate\Http\Request;

class IndustryWebController extends Controller
{
    public function index()
    {
        $partnersCount = IndustryPartner::count();
        $activePrograms = IndustryProgram::where('status', 'active')->count();
        $enrolledStudents = IndustryStudent::where('status', 'active')->count();
        $partners = IndustryPartner::withCount('programs')->orderBy('name')->get();
        $programs = IndustryProgram::with('partner')->orderBy('name')->get();
        $students = IndustryStudent::with('program.partner', 'student', 'mentor')->orderBy('created_at', 'desc')->take(10)->get();
        return view('industry.index', compact(
            'partnersCount', 'activePrograms', 'enrolledStudents',
            'partners', 'programs', 'students'
        ));
    }

    public function partners()
    {
        $partners = IndustryPartner::withCount('programs')->orderBy('name')->paginate(15);
        return view('industry.partners', compact('partners'));
    }

    public function storePartner(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'cooperation_type' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
        ]);
        IndustryPartner::create($d);
        return redirect()->route('industry.partners')->with('success', 'Mitra industri tersimpan');
    }

    public function updatePartner(Request $r, IndustryPartner $partner)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'cooperation_type' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
        ]);
        $partner->update($d);
        return redirect()->route('industry.partners')->with('success', 'Mitra industri diperbarui');
    }

    public function deletePartner(IndustryPartner $partner)
    {
        $partner->delete();
        return redirect()->route('industry.partners')->with('success', 'Mitra industri dihapus');
    }

    public function programs()
    {
        $programs = IndustryProgram::with('partner')->withCount('students')->orderBy('name')->paginate(15);
        $partners = IndustryPartner::where('status', 'active')->orderBy('name')->get();
        return view('industry.programs', compact('programs', 'partners'));
    }

    public function storeProgram(Request $r)
    {
        $d = $r->validate([
            'partner_id' => 'required|exists:industry_partners,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
        ]);
        IndustryProgram::create($d);
        return redirect()->route('industry.programs')->with('success', 'Program industri tersimpan');
    }

    public function updateProgram(Request $r, IndustryProgram $program)
    {
        $d = $r->validate([
            'partner_id' => 'required|exists:industry_partners,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
        ]);
        $program->update($d);
        return redirect()->route('industry.programs')->with('success', 'Program industri diperbarui');
    }

    public function deleteProgram(IndustryProgram $program)
    {
        $program->delete();
        return redirect()->route('industry.programs')->with('success', 'Program industri dihapus');
    }

    public function students()
    {
        $students = IndustryStudent::with('program.partner', 'student', 'mentor')->orderBy('created_at', 'desc')->paginate(15);
        $programs = IndustryProgram::with('partner')->where('status', 'active')->orderBy('name')->get();
        $studentsList = User::whereHas('roles', fn($q) => $q->where('name', 'siswa'))->orderBy('name')->get();
        $mentors = User::whereHas('roles', fn($q) => $q->whereIn('name', ['guru', 'wali-kelas']))->orderBy('name')->get();
        return view('industry.students', compact('students', 'programs', 'studentsList', 'mentors'));
    }

    public function assignStudent(Request $r)
    {
        $d = $r->validate([
            'program_id' => 'required|exists:industry_programs,id',
            'student_id' => 'required|exists:users,id',
            'mentor_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,completed,dropped',
        ]);
        IndustryStudent::create($d);
        return redirect()->route('industry.students')->with('success', 'Siswa ditugaskan ke program');
    }

    public function updateStudentStatus(Request $r, IndustryStudent $industryStudent)
    {
        $d = $r->validate([
            'status' => 'required|in:active,completed,dropped',
            'mentor_id' => 'nullable|exists:users,id',
            'end_date' => 'nullable|date',
        ]);
        $industryStudent->update($d);
        return redirect()->route('industry.students')->with('success', 'Status siswa diperbarui');
    }

    public function removeStudent(IndustryStudent $industryStudent)
    {
        $industryStudent->delete();
        return redirect()->route('industry.students')->with('success', 'Siswa dihapus dari program');
    }
}
