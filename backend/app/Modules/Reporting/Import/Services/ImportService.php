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

    public function importClasses(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, ['nama' => 'required|string|max:255', 'jurusan' => 'nullable|string', 'tingkat' => 'nullable|integer']);
            if ($validator->fails()) { $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all()); continue; }
            try {
                \App\Modules\Academic\Class\Models\Kelas::create(['name' => $row['nama'], 'jurusan' => $row['jurusan'] ?? null, 'tingkat' => $row['tingkat'] ?? null]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage(); }
        }
        return compact('imported', 'errors');
    }

    public function importSubjects(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, ['nama' => 'required|string|max:255', 'kode' => 'nullable|string|unique:subjects,code', 'jam' => 'nullable|integer']);
            if ($validator->fails()) { $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all()); continue; }
            try {
                \App\Modules\Academic\Subject\Models\Subject::create(['name' => $row['nama'], 'code' => $row['kode'] ?? null, 'jam_per_minggu' => $row['jam'] ?? null]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage(); }
        }
        return compact('imported', 'errors');
    }

    public function importSchedules(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, [
                'kelas' => 'required|string', 'mapel' => 'required|string', 'hari' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
                'jam_mulai' => 'required', 'jam_selesai' => 'required',
            ]);
            if ($validator->fails()) { $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all()); continue; }
            try {
                $class = \App\Modules\Academic\Class\Models\Kelas::where('name', $row['kelas'])->first();
                $subject = \App\Modules\Academic\Subject\Models\Subject::where('name', $row['mapel'])->first();
                if (!$class) { $errors[] = "Baris " . ($i + 1) . ": Kelas '{$row['kelas']}' tidak ditemukan"; continue; }
                if (!$subject) { $errors[] = "Baris " . ($i + 1) . ": Mapel '{$row['mapel']}' tidak ditemukan"; continue; }
                \App\Modules\Academic\Schedule\Models\Schedule::create([
                    'class_id' => $class->id, 'subject_id' => $subject->id,
                    'day_of_week' => $row['hari'], 'start_time' => $row['jam_mulai'], 'end_time' => $row['jam_selesai'],
                    'teacher_id' => $row['guru_id'] ?? auth()->id(),
                ]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage(); }
        }
        return compact('imported', 'errors');
    }

    public function importTeachers(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, ['nama' => 'required|string|max:255', 'nip' => 'nullable|string', 'email' => 'nullable|email|unique:users,email']);
            if ($validator->fails()) { $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all()); continue; }
            try {
                User::create([
                    'name' => $row['nama'], 'nip' => $row['nip'] ?? null, 'email' => $row['email'] ?? null,
                    'password' => bcrypt('password'), 'role' => 'teacher',
                    'phone' => $row['telepon'] ?? $row['phone'] ?? null,
                ]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage(); }
        }
        return compact('imported', 'errors');
    }

    public function importPayments(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            $validator = Validator::make($row, [
                'nama_siswa' => 'required|string', 'tagihan' => 'required|string', 'jumlah' => 'required|numeric',
                'jatuh_tempo' => 'required|date',
            ]);
            if ($validator->fails()) { $errors[] = "Baris " . ($i + 1) . ": " . implode(', ', $validator->errors()->all()); continue; }
            try {
                $student = User::where('name', $row['nama_siswa'])->where('role', 'student')->first();
                if (!$student) { $errors[] = "Baris " . ($i + 1) . ": Siswa '{$row['nama_siswa']}' tidak ditemukan"; continue; }
                \App\Modules\Finance\Fee\Models\Invoice::create([
                    'student_id' => $student->id, 'description' => $row['tagihan'],
                    'amount' => $row['jumlah'], 'due_date' => $row['jatuh_tempo'],
                    'status' => 'unpaid',
                ]);
                $imported++;
            } catch (\Exception $e) { $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage(); }
        }
        return compact('imported', 'errors');
    }
}
