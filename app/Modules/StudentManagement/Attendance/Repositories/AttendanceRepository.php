<?php

namespace App\Modules\StudentManagement\Attendance\Repositories;

use App\Modules\StudentManagement\Attendance\Interfaces\AttendanceRepositoryInterface;
use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function __construct(
        protected AttendanceRecord $model,
    ) {}

    public function find(int $id): ?AttendanceRecord
    {
        return $this->model->find($id);
    }

    public function findByStudentAndDate(int $studentId, string $date): ?AttendanceRecord
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('date', $date)
            ->first();
    }

    public function getAll(array $filters = []): iterable
    {
        $query = $this->model->newQuery()->with('student');
        $allowed = ['student_id', 'date', 'status'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('student');
        $allowed = ['student_id', 'date', 'status'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function create(array $data): AttendanceRecord
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?AttendanceRecord
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}
