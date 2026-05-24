<?php

namespace App\Modules\Academic\Schedule\Repositories;

use App\Modules\Academic\Schedule\Interfaces\ScheduleRepositoryInterface;
use App\Modules\Academic\Schedule\Models\Schedule;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    public function __construct(protected Schedule $model) {}

    public function find(int $id)
    {
        return $this->model->with(['class', 'subject', 'teacher'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $schedule = $this->find($id);
        if ($schedule) {
            $schedule->update($data);
            return $schedule;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $schedule = $this->find($id);
        if ($schedule) {
            return $schedule->delete();
        }
        return false;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery()->with(['class', 'subject', 'teacher']);
        $allowed = ['class_id', 'subject_id', 'teacher_id', 'day_of_week', 'room'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery()->with(['class', 'subject', 'teacher']);
        $allowed = ['class_id', 'subject_id', 'teacher_id', 'day_of_week', 'room'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->paginate($perPage);
    }
}
