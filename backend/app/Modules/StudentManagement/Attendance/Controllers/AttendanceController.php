<?php

namespace App\Modules\StudentManagement\Attendance\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\StudentManagement\Attendance\Requests\StoreAttendanceRequest;
use App\Modules\StudentManagement\Attendance\Requests\UpdateAttendanceRequest;
use App\Modules\StudentManagement\Attendance\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @group Attendance
 *
 * APIs for managing attendance
 */
class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
    ) {}

    /**
     * Get all attendance records with optional filters
     */
    public function getAllAttendance(Request $request)
    {
        try {
            $filters = $request->only(['student_id', 'date', 'status']);
            $records = $this->attendanceService->getAll($filters);

            return $this->success($records);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of attendance records
     */
    public function getAttendancePaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $records = $this->attendanceService->paginate($perPage, $filters);

            return $this->paginated($records);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated attendance', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get an attendance record by ID
     */
    public function getAttendance($id)
    {
        try {
            $record = $this->attendanceService->getAll(['id' => $id])->first();
            if (! $record) {
                return $this->notFound('Attendance record not found');
            }

            return $this->success($record);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance record', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new attendance record
     */
    public function createAttendance(StoreAttendanceRequest $request)
    {
        try {
            $record = $this->attendanceService->create($request->validated());

            return $this->created($record, 'Attendance record created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating attendance', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an attendance record
     */
    public function updateAttendance(UpdateAttendanceRequest $request, $id)
    {
        try {
            $record = $this->attendanceService->update((int) $id, $request->validated());
            if (! $record) {
                return $this->notFound('Attendance record not found');
            }

            return $this->success($record, 'Attendance record updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating attendance', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete an attendance record
     */
    public function deleteAttendance($id)
    {
        try {
            $result = $this->attendanceService->delete((int) $id);
            if (! $result) {
                return $this->notFound('Attendance record not found');
            }

            return $this->deleted('Attendance record deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting attendance', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }
}
