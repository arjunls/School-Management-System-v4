<?php

namespace App\Modules\Academic\Schedule\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Academic\Schedule\Requests\StoreScheduleRequest;
use App\Modules\Academic\Schedule\Requests\UpdateScheduleRequest;
use App\Modules\Academic\Schedule\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            return $this->success($this->scheduleService->getAllSchedules($request->only([
                'class_id', 'subject_id', 'teacher_id', 'day_of_week'
            ])));
        } catch (\Exception $e) {
            Log::error('Error fetching schedules', ['exception' => $e]);
            return $this->error('Internal server error', 500);
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

            return $this->paginated($schedules);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated schedules', ['exception' => $e]);
            return $this->error('Internal server error', 500);
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
                return $this->notFound('Schedule not found');
            }

            return $this->success($schedule);
        } catch (\Exception $e) {
            Log::error('Error fetching schedule', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new schedule
     */
    public function createSchedule(StoreScheduleRequest $request)
    {
        try {
            $schedule = $this->scheduleService->createSchedule($request->validated());

            return $this->created($schedule, 'Schedule created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating schedule', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing schedule
     */
    public function updateSchedule(UpdateScheduleRequest $request, $id)
    {
        try {
            $schedule = $this->scheduleService->updateSchedule((int) $id, $request->validated());
            if (! $schedule) {
                return $this->notFound('Schedule not found');
            }

            return $this->success($schedule, 'Schedule updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating schedule', ['exception' => $e]);
            return $this->error('Internal server error', 500);
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
                return $this->notFound('Schedule not found');
            }

            return $this->deleted('Schedule deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting schedule', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }
}
