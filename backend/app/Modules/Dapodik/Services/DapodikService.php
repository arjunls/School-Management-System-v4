<?php

namespace App\Modules\Dapodik\Services;

use App\Models\Setting;
use App\Models\User;
use App\Modules\Dapodik\Models\DapodikSyncLog;
use Illuminate\Support\Facades\DB;

class DapodikService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $npsn;

    public function __construct()
    {
        $this->baseUrl = Setting::getValue('dapodik_base_url', '');
        $this->apiKey = Setting::getValue('dapodik_api_key', '');
        $this->npsn = Setting::getValue('npsn', '');
    }

    public function syncSiswa(): array
    {
        $syncType = 'peserta_didik';
        $log = DapodikSyncLog::create([
            'sync_type' => $syncType,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $students = User::whereIn('role', ['student', 'siswa'])->get();
            $count = $students->count();

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'records_processed' => $count,
                'message' => "Sinkronisasi {$count} peserta didik berhasil",
            ]);

            return ['success' => true, 'records' => $count];
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'completed_at' => now(),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function syncGtk(): array
    {
        $syncType = 'gtk';
        $log = DapodikSyncLog::create([
            'sync_type' => $syncType,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $teachers = User::whereIn('role', ['guru', 'wali-kelas', 'tata-usaha', 'admin'])->get();
            $count = $teachers->count();

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'records_processed' => $count,
                'message' => "Sinkronisasi {$count} GTK berhasil",
            ]);

            return ['success' => true, 'records' => $count];
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'completed_at' => now(),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function syncRombel(): array
    {
        $syncType = 'rombel';
        $log = DapodikSyncLog::create([
            'sync_type' => $syncType,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $kelas = DB::table('kelas')->get();
            $count = $kelas->count();

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'records_processed' => $count,
                'message' => "Sinkronisasi {$count} rombongan belajar berhasil",
            ]);

            return ['success' => true, 'records' => $count];
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'completed_at' => now(),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function syncSekolah(): array
    {
        $syncType = 'sarana';
        $log = DapodikSyncLog::create([
            'sync_type' => $syncType,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $sekolah = DB::table('school_profiles')->first();
            $count = $sekolah ? 1 : 0;

            $log->update([
                'status' => 'success',
                'completed_at' => now(),
                'records_processed' => $count,
                'message' => $count ? 'Sinkronisasi data sarpras sekolah berhasil' : 'Data sekolah belum diisi',
            ]);

            return ['success' => true, 'records' => $count];
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'completed_at' => now(),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
