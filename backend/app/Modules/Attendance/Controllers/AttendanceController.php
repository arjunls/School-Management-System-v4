<?php

namespace App\Modules\Attendance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

            return response()->json([
                'success' => true,
                'data' => $records,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
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

            return response()->json([
                'success' => true,
                'data' => $records->items(),
                'pagination' => [
                    'total' => $records->total(),
                    'per_page' => $records->perPage(),
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated attendance', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $record,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching attendance record', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Create a new attendance record
     */
    public function createAttendance(Request $request)
    {
        try {
            $record = $this->attendanceService->create($request->only([
                'student_id', 'date', 'status', 'notes'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Attendance record created successfully',
                'data' => $record,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating attendance', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Update an attendance record
     */
    public function updateAttendance(Request $request, $id)
    {
        try {
            $record = $this->attendanceService->update((int) $id, $request->only([
                'status', 'notes'
            ]));
            if (! $record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance record updated successfully',
                'data' => $record,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating attendance', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting attendance', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
