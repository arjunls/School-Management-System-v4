<?php

namespace App\Modules\Reporting\Export\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Exports
 *
 * APIs for exporting data
 */
class ExportController extends Controller
{
    /**
     * Export students to CSV
     */
    public function studentsCSV()
    {
        try {
            $students = User::where('role', 'student')->with('kelas')->get();
            $filename = 'students-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($students) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['NISN', 'Name', 'Email', 'Gender', 'Phone', 'Class', 'Status', 'Date of Birth', 'Address']);

                foreach ($students as $s) {
                    fputcsv($handle, [
                        $s->nisn ?? '—',
                        $s->name,
                        $s->email,
                        $s->gender ?? '—',
                        $s->phone ?? '—',
                        $s->kelas?->name ?? '—',
                        $s->status ?? 'active',
                        $s->date_of_birth ?? '—',
                        $s->address ?? '—',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting students CSV', ['exception' => $e]);
            return $this->error();
        }
    }

    /**
     * Export teachers to CSV
     */
    public function teachersCSV()
    {
        try {
            $teachers = User::where('role', 'teacher')->get();
            $filename = 'teachers-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($teachers) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Name', 'Email', 'Gender', 'Phone', 'Status', 'Date of Birth', 'Address']);

                foreach ($teachers as $t) {
                    fputcsv($handle, [
                        $t->name, $t->email, $t->gender ?? '—', $t->phone ?? '—',
                        $t->status ?? 'active', $t->date_of_birth ?? '—', $t->address ?? '—',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting teachers CSV', ['exception' => $e]);
            return $this->error();
        }
    }

    /**
     * Export classes to CSV
     */
    public function classesCSV()
    {
        try {
            $classes = Kelas::with('homeroomTeacher')->withCount('students')->get();
            $filename = 'classes-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($classes) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Name', 'Grade Level', 'Homeroom Teacher', 'Capacity', 'Student Count']);

                foreach ($classes as $c) {
                    fputcsv($handle, [
                        $c->name, $c->grade_level, $c->homeroomTeacher?->name ?? '—',
                        $c->capacity, $c->students_count,
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting classes CSV', ['exception' => $e]);
            return $this->error();
        }
    }

    /**
     * Export subjects to CSV
     */
    public function subjectsCSV()
    {
        try {
            $subjects = Subject::with('teacher')->get();
            $filename = 'subjects-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($subjects) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Code', 'Name', 'Credits', 'Teacher']);

                foreach ($subjects as $s) {
                    fputcsv($handle, [$s->code, $s->name, $s->credits, $s->teacher?->name ?? '—']);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting subjects CSV', ['exception' => $e]);
            return $this->error();
        }
    }

    /**
     * Export grades to CSV
     */
    public function gradesCSV()
    {
        try {
            $grades = Grade::with(['student', 'subject'])->get();
            $filename = 'grades-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($grades) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Student', 'NISN', 'Subject', 'Score', 'Grade', 'Term']);

                foreach ($grades as $g) {
                    fputcsv($handle, [
                        $g->student?->name ?? '—',
                        $g->student?->nisn ?? '—',
                        $g->subject?->name ?? '—',
                        $g->score ?? '—',
                        $g->grade ?? '—',
                        $g->term ?? '—',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting grades CSV', ['exception' => $e]);
            return $this->error();
        }
    }

    /**
     * Export attendance records to CSV
     */
    public function attendanceCSV()
    {
        try {
            $records = AttendanceRecord::with('student')->orderBy('date', 'desc')->get();
            $filename = 'attendance-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($records) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Date', 'Student', 'NISN', 'Status', 'Notes']);

                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->date, $r->student?->name ?? '—', $r->student?->nisn ?? '—',
                        $r->status, $r->notes ?? '—',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            Log::error('Error exporting attendance CSV', ['exception' => $e]);
            return $this->error();
        }
    }
}
