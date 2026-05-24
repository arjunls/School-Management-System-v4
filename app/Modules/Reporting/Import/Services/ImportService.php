<?php

namespace App\Modules\Reporting\Import\Services;

use App\Models\User;
use App\Modules\Learning\Grade\Models\Grade;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    public function importStudents(array $rows, string $kelasId = null): array
    {
        $imported = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, [
                'nama' => 'required|string|max:255',
                'nisn' => 'nullable|string|max:20',
                'jenis_kelamin' => 'nullable|in:male,female,Laki-laki,Perempuan',
                'email' => 'nullable|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            try {
                User::create([
                    'name' => $row['nama'],
                    'nisn' => $row['nisn'] ?? null,
                    'email' => $row['email'] ?? null,
                    'password' => bcrypt('password'),
                    'role' => 'siswa',
                    'kelas_id' => $kelasId,
                    'gender' => match ($row['jenis_kelamin'] ?? null) {
                        'Laki-laki', 'male' => 'male',
                        'Perempuan', 'female' => 'female',
                        default => null,
                    },
                    'phone' => $row['telepon'] ?? $row['phone'] ?? null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return compact('imported', 'errors');
    }

    public function importGrades(array $rows): array
    {
        $imported = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, [
                'nis' => 'required|exists:users,id',
                'mata_pelajaran' => 'required|string',
                'nilai' => 'required|numeric|min:0|max:100',
                'semester' => 'nullable|string',
                'tahun_ajaran' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            try {
                Grade::create([
                    'student_id' => $row['nis'],
                    'subject_id' => $row['mata_pelajaran'],
                    'score' => $row['nilai'],
                    'term' => $row['semester'] ?? null,
                    'academic_year_id' => $row['tahun_ajaran'] ?? null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return compact('imported', 'errors');
    }

    public function importAttendance(array $rows): array
    {
        $imported = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, [
                'nis' => 'required|exists:users,id',
                'tanggal' => 'required|date',
                'status' => 'required|in:hadir,sakit,izin,alpha',
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            try {
                AttendanceRecord::updateOrCreate(
                    ['student_id' => $row['nis'], 'date' => $row['tanggal']],
                    ['status' => $row['status'], 'note' => $row['keterangan'] ?? null]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return compact('imported', 'errors');
    }
}
