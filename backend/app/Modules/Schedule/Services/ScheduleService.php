<?php

namespace App\Modules\Schedule\Services;

use App\Modules\Schedule\Interfaces\ScheduleRepositoryInterface;

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
        return $this->repository->create($data);
    }

    public function updateSchedule(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSchedule(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
