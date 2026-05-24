<?php

namespace App\Modules\Reporting\Export\Services;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Finance\Fee\Models\FeeInvoice;
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

    public function exportKelas(): StreamedResponse
    {
        $kelas = Kelas::with('homeroomTeacher')->get();
        return $this->csvResponse('data-kelas.csv', [
            ['Nama Kelas', 'Tingkat', 'Wali Kelas', 'Kapasitas', 'Jumlah Siswa'],
        ], function () use ($kelas) {
            foreach ($kelas as $k) {
                yield [
                    $k->name,
                    $k->grade_level,
                    $k->homeroomTeacher?->name,
                    $k->capacity,
                    $k->students()->count(),
                ];
            }
        });
    }

    public function exportJadwal(): StreamedResponse
    {
        $jadwal = Schedule::with(['class', 'subject', 'teacher'])->get();
        return $this->csvResponse('data-jadwal.csv', [
            ['Kelas', 'Mata Pelajaran', 'Guru', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Ruang'],
        ], function () use ($jadwal) {
            foreach ($jadwal as $j) {
                yield [
                    $j->class?->name,
                    $j->subject?->name,
                    $j->teacher?->name,
                    $j->day_of_week,
                    $j->start_time,
                    $j->end_time,
                    $j->room,
                ];
            }
        });
    }

    public function exportPembayaran(): StreamedResponse
    {
        $invoices = FeeInvoice::with(['student', 'feeType'])->get();
        return $this->csvResponse('data-pembayaran.csv', [
            ['NIS', 'Nama Siswa', 'Jenis Tagihan', 'Jumlah', 'Jatuh Tempo', 'Status'],
        ], function () use ($invoices) {
            foreach ($invoices as $inv) {
                yield [
                    $inv->student_id,
                    $inv->student?->name,
                    $inv->feeType?->name,
                    $inv->amount,
                    $inv->due_date,
                    $inv->status,
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
