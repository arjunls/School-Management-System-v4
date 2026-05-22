<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Schedule\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * @group Schedules
 *
 * APIs for managing schedules
 */
class ScheduleController extends Controller
{
    public function __construct(protected ScheduleService $scheduleService) {}

    /**
     * Get all schedules with optional filters
     */
    public function getAllSchedules(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->scheduleService->getAllSchedules($request->only([
                    'class_id', 'subject_id', 'teacher_id', 'day_of_week'
                ])),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedules', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Get paginated list of schedules
     */
    public function getSchedulesPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $schedules = $this->scheduleService->getSchedulesPaginated($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => $schedules->items(),
                'pagination' => [
                    'total' => $schedules->total(),
                    'per_page' => $schedules->perPage(),
                    'current_page' => $schedules->currentPage(),
                    'last_page' => $schedules->lastPage(),
                    'from' => $schedules->firstItem(),
                    'to' => $schedules->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated schedules', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Get a schedule by ID
     */
    public function getSchedule($id)
    {
        try {
            $schedule = $this->scheduleService->getSchedule((int) $id);
            if (! $schedule) {
                return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $schedule]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedule', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Create a new schedule
     */
    public function createSchedule(Request $request)
    {
        try {
            $schedule = $this->scheduleService->createSchedule($request->only([
                'class_id', 'subject_id', 'teacher_id', 'day_of_week',
                'start_time', 'end_time', 'room'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'data' => $schedule,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating schedule', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Update an existing schedule
     */
    public function updateSchedule(Request $request, $id)
    {
        try {
            $schedule = $this->scheduleService->updateSchedule((int) $id, $request->only([
                'class_id', 'subject_id', 'teacher_id', 'day_of_week',
                'start_time', 'end_time', 'room'
            ]));
            if (! $schedule) {
                return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Schedule updated successfully', 'data' => $schedule]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating schedule', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule($id)
    {
        try {
            $result = $this->scheduleService->deleteSchedule((int) $id);
            if (! $result) {
                return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Schedule deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting schedule', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }
}
