<?php

namespace App\Modules\Backup\Controllers;

use App\Kernel\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect();
        if (Storage::disk('local')->exists('backups')) {
            $files = Storage::disk('local')->files('backups');
            $backups = collect($files)->map(function ($f) {
                return [
                    'name' => basename($f),
                    'size' => Storage::disk('local')->size($f),
                    'date' => Storage::disk('local')->lastModified($f),
                ];
            })->sortByDesc('date');
        }
        return view('backup.index', compact('backups'));
    }

    public function create()
    {
        $filename = 'backup-' . now()->format('Y-md-Hi') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $config = config('database.connections.mysql');
        $cmd = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['host']),
            escapeshellarg($config['database']),
            escapeshellarg($path)
        );

        $output = null;
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0) {
            return redirect()->route('backup.index')->with('error', 'Gagal membuat backup: ' . implode("\n", $output));
        }

        return redirect()->route('backup.index')->with('success', 'Backup berhasil dibuat: ' . $filename);
    }

    public function download(string $filename)
    {
        $path = 'backups/' . basename($filename);
        if (!Storage::disk('local')->exists($path)) {
            return redirect()->route('backup.index')->with('error', 'File tidak ditemukan');
        }
        return Storage::disk('local')->download($path);
    }

    public function destroy(string $filename)
    {
        $path = 'backups/' . basename($filename);
        Storage::disk('local')->delete($path);
        return redirect()->route('backup.index')->with('success', 'Backup berhasil dihapus');
    }
}
