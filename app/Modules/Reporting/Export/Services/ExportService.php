<?php

namespace App\Modules\Reporting\Export\Services;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportStudents(): StreamedResponse
    {
        $students = User::where('role', 'siswa')->with('kelas')->get();
        return $this->csvResponse('data-siswa.csv', [
            ['NIS', 'NISN', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Kelas', 'Jurusan', 'Alamat', 'Telepon', 'Status'],
        ], function () use ($students) {
            foreach ($students as $s) {
                yield [
                    $s->id,
                    $s->nisn,
                    $s->name,
                    $s->gender === 'male' ? 'Laki-laki' : 'Perempuan',
                    $s->tempat_lahir,
                    $s->date_of_birth?->format('Y-m-d'),
                    $s->kelas?->name,
                    $s->jurusan,
                    $s->alamat,
                    $s->phone,
                    $s->status,
                ];
            }
        });
    }

    public function exportTeachers(): StreamedResponse
    {
        $teachers = User::where('role', 'guru')->get();
        return $this->csvResponse('data-guru.csv', [
            ['NIP', 'Nama', 'Jenis Kelamin', 'Tanggal Lahir', 'Email', 'Telepon', 'Alamat', 'Status'],
        ], function () use ($teachers) {
            foreach ($teachers as $t) {
                yield [
                    $t->id,
                    $t->name,
                    $t->gender === 'male' ? 'Laki-laki' : 'Perempuan',
                    $t->date_of_birth?->format('Y-m-d'),
                    $t->email,
                    $t->phone,
                    $t->address,
                    $t->status,
                ];
            }
        });
    }

    public function exportGrades(): StreamedResponse
    {
        $grades = Grade::with(['student', 'subject'])->get();
        return $this->csvResponse('data-nilai.csv', [
            ['NIS', 'Nama Siswa', 'Mata Pelajaran', 'Nilai', 'Semester', 'Tahun Ajaran'],
        ], function () use ($grades) {
            foreach ($grades as $g) {
                yield [
                    $g->student_id,
                    $g->student?->name,
                    $g->subject?->name,
                    $g->score,
                    $g->term,
                    $g->academic_year_id,
                ];
            }
        });
    }

    public function exportAttendance(): StreamedResponse
    {
        $records = AttendanceRecord::with('student')->get();
        return $this->csvResponse('data-kehadiran.csv', [
            ['NIS', 'Nama Siswa', 'Tanggal', 'Status', 'Keterangan'],
        ], function () use ($records) {
            foreach ($records as $r) {
                yield [
                    $r->student_id,
                    $r->student?->name,
                    $r->date,
                    $r->status,
                    $r->note ?? '-',
                ];
            }
        });
    }

    private function csvResponse(string $filename, array $headers, \Closure $dataGenerator): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($headers, $dataGenerator) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($dataGenerator() as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }
}
