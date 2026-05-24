<?php

namespace App\Modules\Reporting\Export\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Reporting\Export\Services\ExportService;

class ExportController extends Controller
{
    public function __construct(private ExportService $exportService) {}

    public function students()
    {
        return $this->exportService->exportStudents();
    }

    public function teachers()
    {
        return $this->exportService->exportTeachers();
    }

    public function grades()
    {
        return $this->exportService->exportGrades();
    }

    public function attendance()
    {
        return $this->exportService->exportAttendance();
    }
}
