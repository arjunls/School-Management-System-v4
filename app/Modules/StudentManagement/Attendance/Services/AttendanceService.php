<?php

namespace App\Modules\StudentManagement\Attendance\Services;

use App\Modules\StudentManagement\Attendance\Interfaces\AttendanceRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepositoryInterface $repository,
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data)
    {
        $data['created_by'] = Auth::id();

        // Upsert: update if exists for same student+date, otherwise create
        $existing = $this->repository->findByStudentAndDate(
            $data['student_id'],
            $data['date'],
        );

        if ($existing) {
            $this->repository->update($existing->id, $data);
            return $existing->fresh();
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
