<?php

namespace App\Modules\StaffManagement\Teacher\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use App\Modules\Academic\AcademicYear\Models\Term;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class GuruPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $homeroomClasses = Kelas::with('students')->where('homeroom_teacher_id', $user->id)->get();
        $subjectClasses = Schedule::with('class')->where('teacher_id', $user->id)->get()->pluck('class')->unique('id');
        $allClasses = $homeroomClasses->merge($subjectClasses)->unique('id');
        $totalStudents = User::whereIn('kelas_id', $allClasses->pluck('id'))->where('role', 'student')->count();
        $totalSchedules = Schedule::where('teacher_id', $user->id)->count();
        $totalGrades = Grade::whereIn('student_id', User::whereIn('kelas_id', $allClasses->pluck('id'))->where('role', 'student')->pluck('id'))->count();

        return view('guru.portal.dashboard', compact('user', 'allClasses', 'totalStudents', 'totalSchedules', 'totalGrades', 'homeroomClasses'));
    }

    public function schedule()
    {
        $user = auth()->user();
        $schedules = Schedule::with(['class', 'subject'])
            ->where('teacher_id', $user->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        $days = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu'];
        return view('guru.portal.schedule', compact('schedules', 'days', 'user'));
    }

    public function grades(Request $request, Kelas $kelas)
    {
        $user = auth()->user();
        $this->authorizeTeacherAccess($user, $kelas);

        $students = User::where('kelas_id', $kelas->id)->where('role', 'student')->orderBy('name')->get();
        $subjects = Subject::whereHas('schedules', fn($q) => $q->where('teacher_id', $user->id)->where('class_id', $kelas->id))->get();
        $terms = Term::whereHas('academicYear', fn($q) => $q->where('is_active', true))->get();

        $selectedSubject = $request->get('subject_id');
        $selectedTerm = $request->get('term_id');

        $grades = Grade::whereIn('student_id', $students->pluck('id'))
            ->when($selectedSubject, fn($q) => $q->where('subject_id', $selectedSubject))
            ->when($selectedTerm, fn($q) => $q->where('term_id', $selectedTerm))
            ->get()
            ->groupBy('student_id');

        return view('guru.portal.grades', compact('students', 'subjects', 'terms', 'grades', 'kelas', 'selectedSubject', 'selectedTerm'));
    }

    public function storeGrades(Request $request, Kelas $kelas)
    {
        $user = auth()->user();
        $this->authorizeTeacherAccess($user, $kelas);

        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0|max:100',
        ]);

        foreach ($data['scores'] as $studentId => $score) {
            Grade::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $data['subject_id'], 'term_id' => $data['term_id']],
                ['score' => $score]
            );
        }

        return redirect()->route('guru.portal.grades', $kelas)->with('success', 'Nilai berhasil disimpan');
    }

    public function attendance(Request $request, Kelas $kelas)
    {
        $user = auth()->user();
        $this->authorizeTeacherAccess($user, $kelas);

        $students = User::where('kelas_id', $kelas->id)->where('role', 'student')->orderBy('name')->get();
        $date = $request->get('date', now()->format('Y-m-d'));

        $records = AttendanceRecord::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('guru.portal.attendance', compact('students', 'kelas', 'date', 'records'));
    }

    public function storeAttendance(Request $request, Kelas $kelas)
    {
        $user = auth()->user();
        $this->authorizeTeacherAccess($user, $kelas);

        $data = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:hadir,sakit,izin,alpha',
            'attendance.*.notes' => 'nullable|string',
        ]);

        foreach ($data['attendance'] as $studentId => $val) {
            AttendanceRecord::updateOrCreate(
                ['student_id' => $studentId, 'date' => $data['date']],
                ['status' => $val['status'], 'notes' => $val['notes'] ?? null]
            );
        }

        return redirect()->route('guru.portal.attendance', [$kelas, 'date' => $data['date']])->with('success', 'Kehadiran berhasil disimpan');
    }

    private function authorizeTeacherAccess($user, $kelas)
    {
        $isHomeroom = $kelas->homeroom_teacher_id === $user->id;
        $isSubjectTeacher = Schedule::where('teacher_id', $user->id)->where('class_id', $kelas->id)->exists();
        if (!$isHomeroom && !$isSubjectTeacher) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini');
        }
    }
}
