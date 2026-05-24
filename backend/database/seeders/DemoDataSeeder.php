<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Academic\Schedule\Models\Schedule;
use App\Models\Document;
use App\Modules\Finance\Fee\Models\FeeType;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Finance\Fee\Models\FeePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Teachers
        $guruData = [
            ['Siti Guru Matematika', 'guru2@school.com', 'female'],
            ['Ahmad Guru Fisika', 'guru3@school.com', 'male'],
            ['Dewi Guru Bahasa Inggris', 'guru4@school.com', 'female'],
            ['Rudi Guru RPL', 'guru5@school.com', 'male'],
            ['Maya Guru PKN', 'guru6@school.com', 'female'],
        ];
        $guru = collect();
        foreach ($guruData as $i => $d) {
            $u = User::firstOrCreate(
                ['email' => $d[1]],
                [
                    'name' => $d[0],
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                    'status' => 'active',
                    'phone' => '0812345679' . ($i + 1),
                    'gender' => $d[2],
                ]
            );
            if (!$u->hasRole('guru')) $u->assignRole('guru');
            $guru->push($u);
        }

        // Kelas
        $kelasData = [
            ['X RPL 1', 10, 36], ['X RPL 2', 10, 36],
            ['XI RPL 1', 11, 36], ['XI RPL 2', 11, 36],
            ['XII RPL 1', 12, 36], ['XII RPL 2', 12, 36],
        ];
        $kelas = collect();
        foreach ($kelasData as $k) {
            $kls = Kelas::firstOrCreate(
                ['name' => $k[0]],
                ['grade_level' => $k[1], 'capacity' => $k[2], 'homeroom_teacher_id' => $guru->random()->id]
            );
            $kelas->push($kls);
        }

        // Siswa
        $names = [
            'Ahmad Ramadhan', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Sartika', 'Rudi Hermawan',
            'Rina Marlina', 'Andi Prasetyo', 'Mega Wati', 'Fajar Nugroho', 'Indah Permata',
            'Rizky Fadhilah', 'Nurul Aini', 'Dimas Ardiansyah', 'Putri Ayu', 'Eko Prasetyo',
        ];
        foreach ($names as $i => $nama) {
            $email = strtolower(str_replace(' ', '.', $nama)) . '@school.com';
            $s = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'nisn' => '12345678' . str_pad($i + 10, 2, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'phone' => '081234567' . str_pad($i + 20, 3, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 === 0 ? 'male' : 'female',
                    'kelas_id' => $kelas->random()->id,
                    'jurusan' => 'RPL',
                ]
            );
            if (!$s->hasRole('siswa')) $s->assignRole('siswa');
        }

        // Mapel
        $mapel = [
            ['Matematika', 'MTK', 4], ['Fisika', 'FIS', 3],
            ['Bahasa Inggris', 'BING', 3], ['Bahasa Indonesia', 'BINDO', 3],
            ['Pemrograman RPL', 'RPL', 4], ['Basis Data', 'BD', 3],
            ['Pendidikan Kewarganegaraan', 'PKN', 2], ['Sejarah', 'SJRH', 2],
        ];
        foreach ($mapel as $m) {
            Subject::firstOrCreate(['name' => $m[0]], ['code' => $m[1], 'credits' => $m[2]]);
        }
        $subjects = Subject::all();

        // Jadwal
        if (Schedule::count() < 10) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            $times = [['07:00', '08:30'], ['08:40', '10:10'], ['10:20', '11:50'], ['12:30', '14:00']];
            foreach ($kelas as $k) {
                foreach (range(0, 3) as $t) {
                    Schedule::create([
                        'class_id' => $k->id,
                        'subject_id' => $subjects->random()->id,
                        'teacher_id' => $guru->random()->id,
                        'day_of_week' => $days[array_rand($days)],
                        'start_time' => $times[$t][0],
                        'end_time' => $times[$t][1],
                        'room' => 'R.' . rand(101, 305),
                    ]);
                }
            }
        }

        // Fee Types
        FeeType::firstOrCreate(['name' => 'SPP'], ['description' => 'Sumbangan Pembinaan Pendidikan', 'amount' => 200000, 'frequency' => 'monthly']);
        FeeType::firstOrCreate(['name' => 'LKS'], ['description' => 'Lembar Kerja Siswa', 'amount' => 50000, 'frequency' => 'once']);
        FeeType::firstOrCreate(['name' => 'OSIS'], ['description' => 'Dana OSIS', 'amount' => 25000, 'frequency' => 'monthly']);
        FeeType::firstOrCreate(['name' => 'Praktikum'], ['description' => 'Biaya Praktikum', 'amount' => 100000, 'frequency' => 'once']);

        // Fee Invoices
        if (FeeInvoice::count() < 10) {
            $siswaAll = User::where('role', 'student')->get();
            $feeTypes = FeeType::all();
            foreach ($siswaAll as $s) {
                foreach ($feeTypes->random(2) as $ft) {
                    $due = now()->addMonths(rand(-1, 3));
                    FeeInvoice::create([
                        'fee_type_id' => $ft->id,
                        'student_id' => $s->id,
                        'amount' => $ft->amount,
                        'due_date' => $due,
                        'status' => $due->isPast() ? 'overdue' : 'unpaid',
                    ]);
                }
            }
        }

        $this->command->info('Demo data seeded successfully!');
    }
}
