<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Class\Models\Kelas;
use App\Modules\Grade\Models\Grade;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // Admin account — full access
        $admin = User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
            'address' => 'Jl. Pendidikan No. 1',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
        ]);
        $admin->assignRole('admin');

        // Teacher account
        $teacher = User::factory()->create([
            'name' => 'Budi Guru',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
            'phone' => '081234567891',
            'address' => 'Jl. Mengajar No. 2',
            'date_of_birth' => '1992-06-15',
            'gender' => 'male',
        ]);
        $teacher->assignRole('teacher');

        // Demo classes
        $kelas10 = Kelas::create(['name' => 'X A', 'grade_level' => 10, 'capacity' => 30]);
        $kelas11 = Kelas::create(['name' => 'XI A', 'grade_level' => 11, 'capacity' => 30]);
        $kelas12 = Kelas::create(['name' => 'XII A', 'grade_level' => 12, 'capacity' => 30]);

        // Demo subjects
        $mtk = Subject::create(['name' => 'Mathematics', 'code' => 'MATH101', 'credits' => 4]);
        $ipa = Subject::create(['name' => 'Science', 'code' => 'SCI101', 'credits' => 3]);
        $ing = Subject::create(['name' => 'English', 'code' => 'ENG101', 'credits' => 3]);
        $ips = Subject::create(['name' => 'Social Studies', 'code' => 'SOC101', 'credits' => 2]);
        $ind = Subject::create(['name' => 'Indonesian', 'code' => 'IND101', 'credits' => 3]);

        // Demo students
        $student1 = User::factory()->create([
            'name' => 'Siti Murid',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
            'phone' => '081234567892',
            'address' => 'Jl. Belajar No. 3',
            'date_of_birth' => '2008-03-20',
            'gender' => 'female',
            'nisn' => '1234567890',
            'kelas_id' => $kelas10->id,
        ]);

        $student2 = User::factory()->create([
            'name' => 'Ahmad Santoso',
            'email' => 'ahmad@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
            'phone' => '081234567893',
            'date_of_birth' => '2008-05-12',
            'gender' => 'male',
            'nisn' => '1234567891',
            'kelas_id' => $kelas10->id,
        ]);

        $student3 = User::factory()->create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
            'phone' => '081234567894',
            'date_of_birth' => '2008-07-22',
            'gender' => 'female',
            'nisn' => '1234567892',
            'kelas_id' => $kelas10->id,
        ]);

        foreach ([$student1, $student2, $student3] as $s) {
            $s->assignRole('student');
        }

        // Parent account
        $parent = User::factory()->create([
            'name' => 'Rina Orang Tua',
            'email' => 'parent@school.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'status' => 'active',
            'phone' => '081234567895',
            'date_of_birth' => '1980-05-10',
            'gender' => 'female',
        ]);
        $parent->assignRole('parent');
        $parent->children()->attach([$student1->id => ['relationship' => 'Ibu'], $student2->id => ['relationship' => 'Ibu']]);

        $students = [$student1, $student2, $student3];
        $subjects = [$mtk, $ipa, $ing, $ips, $ind];

        // Academic year & term
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $term = Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester 2',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        // Demo attendance records for the last 7 days
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'sick', 'present'];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->format('Y-m-d');
            foreach ($students as $s) {
                AttendanceRecord::create([
                    'student_id' => $s->id,
                    'date' => $date,
                    'status' => $statuses[array_rand($statuses)],
                    'created_by' => 1,
                    'academic_year_id' => $academicYear->id,
                    'term_id' => $term->id,
                ]);
            }
        }

        // Demo grades
        foreach ($students as $s) {
            foreach ($subjects as $sub) {
                Grade::create([
                    'student_id' => $s->id,
                    'subject_id' => $sub->id,
                    'score' => rand(60, 100),
                    'grade' => '',
                    'term' => '2025/2026 Semester 2',
                    'academic_year_id' => $academicYear->id,
                    'term_id' => $term->id,
                ]);
            }
        }
    }
}
