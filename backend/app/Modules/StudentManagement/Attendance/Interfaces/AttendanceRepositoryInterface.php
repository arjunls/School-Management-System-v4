<?php

namespace App\Modules\StudentManagement\Attendance\Interfaces;

use App\Modules\StudentManagement\Attendance\Models\AttendanceRecord;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceRepositoryInterface
{
    public function find(int $id): ?AttendanceRecord;
    public function findByStudentAndDate(int $studentId, string $date): ?AttendanceRecord;
    public function getAll(array $filters = []): iterable;
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function create(array $data): AttendanceRecord;
    public function update(int $id, array $data): ?AttendanceRecord;
    public function delete(int $id): bool;
}
