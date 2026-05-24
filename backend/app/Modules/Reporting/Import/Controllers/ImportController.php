<?php

namespace App\Modules\Reporting\Import\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Reporting\Import\Services\ImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(private ImportService $importService) {}

    public function students(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importStudents($rows, $request->kelas_id);
        return redirect()->back()->with('import_result', $result);
    }

    public function grades(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importGrades($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function attendance(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importAttendance($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function classes(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importClasses($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function subjects(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importSubjects($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function schedules(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importSchedules($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function teachers(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importTeachers($rows);
        return redirect()->back()->with('import_result', $result);
    }

    public function payments(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = $this->parseCsv($request->file('file'));
        $result = $this->importService->importPayments($rows);
        return redirect()->back()->with('import_result', $result);
    }

    private function parseCsv($file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(function ($h) {
            return strtolower(trim(str_replace([' ', '-'], '_', $h)));
        }, $headers);

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
        return $rows;
    }
}
