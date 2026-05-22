<?php

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Interfaces\AttendanceRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        $validator = Validator::make($data, [
            'student_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,sick,leave',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $data = $validator->validated();
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
        $validator = Validator::make($data, [
            'status' => 'sometimes|required|in:present,absent,sick,leave',
            'notes' => 'sometimes|nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->update($id, $validator->validated());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
