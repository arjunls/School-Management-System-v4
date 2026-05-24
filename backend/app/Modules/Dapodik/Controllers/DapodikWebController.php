<?php

namespace App\Modules\Dapodik\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Dapodik\Models\DapodikSyncLog;
use App\Modules\Dapodik\Services\DapodikService;
use Illuminate\Http\Request;

class DapodikWebController extends Controller
{
    protected DapodikService $dapodikService;

    public function __construct(DapodikService $dapodikService)
    {
        $this->dapodikService = $dapodikService;
    }

    public function index()
    {
        $logs = DapodikSyncLog::latest()->paginate(20);
        $npsn = Setting::getValue('npsn', '');
        $dapodikBaseUrl = Setting::getValue('dapodik_base_url', '');
        $dapodikApiKey = Setting::getValue('dapodik_api_key', '');

        return view('dapodik.index', compact('logs', 'npsn', 'dapodikBaseUrl', 'dapodikApiKey'));
    }

    public function sync(string $type)
    {
        $methodMap = [
            'peserta_didik' => 'syncSiswa',
            'gtk' => 'syncGtk',
            'rombel' => 'syncRombel',
            'sarana' => 'syncSekolah',
        ];

        if (!isset($methodMap[$type])) {
            return redirect()->route('dapodik.index')->with('error', 'Tipe sinkronasi tidak valid');
        }

        $result = $this->dapodikService->{$methodMap[$type]}();

        if ($result['success']) {
            return redirect()->route('dapodik.index')->with('success', $result['message'] ?? 'Sinkronasi berhasil');
        }

        return redirect()->route('dapodik.index')->with('error', $result['message'] ?? 'Sinkronasi gagal');
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'npsn' => 'required|string|max:20',
            'dapodik_base_url' => 'required|url',
            'dapodik_api_key' => 'required|string',
        ]);

        Setting::setValue('npsn', $request->input('npsn'));
        Setting::setValue('dapodik_base_url', $request->input('dapodik_base_url'));
        Setting::setValue('dapodik_api_key', $request->input('dapodik_api_key'));

        return redirect()->route('dapodik.index')->with('success', 'Konfigurasi Dapodik berhasil disimpan');
    }
}
