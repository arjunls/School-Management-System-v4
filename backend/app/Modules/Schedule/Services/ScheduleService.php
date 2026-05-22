<?php

namespace App\Modules\Schedule\Services;

use App\Modules\Schedule\Interfaces\ScheduleRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ScheduleService
{
    public function __construct(protected ScheduleRepositoryInterface $repository) {}

    public function getSchedule(int $id)
    {
        return $this->repository->find($id);
    }

    public function getAllSchedules(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getSchedulesPaginated(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function createSchedule(array $data)
    {
        $validator = Validator::make($data, [
            'class_id' => 'required|integer|exists:kelas,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'teacher_id' => 'nullable|integer|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->create($validator->validated());
    }

    public function updateSchedule(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'class_id' => 'sometimes|required|integer|exists:kelas,id',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
            'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'room' => 'sometimes|nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->update($id, $validator->validated());
    }

    public function deleteSchedule(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
