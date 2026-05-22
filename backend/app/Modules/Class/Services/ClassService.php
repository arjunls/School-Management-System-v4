<?php

namespace App\Modules\Class\Services;

use App\Models\User;
use App\Modules\Class\Repositories\ClassRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClassService
{
    public function __construct(protected ClassRepository $repository) {}

    public function getClass(int $id)
    {
        return $this->repository->find($id);
    }

    public function getAllClasses(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getClassesPaginated(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function createClass(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|integer|exists:users,id',
            'capacity' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->create($validator->validated());
    }

    public function updateClass(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:50',
            'grade_level' => 'sometimes|required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'sometimes|nullable|integer|exists:users,id',
            'capacity' => 'sometimes|nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->update($id, $validator->validated());
    }

    public function deleteClass(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function addStudentToClass(int $classId, int $studentId)
    {
        $class = $this->repository->find($classId);
        if (! $class) {
            throw new \RuntimeException('Class not found');
        }

        $student = User::where('id', $studentId)->where('role', 'student')->first();
        if (! $student) {
            throw new \RuntimeException('Student not found');
        }

        $student->update(['kelas_id' => $classId]);

        return $student;
    }

    public function removeStudentFromClass(int $classId, int $studentId)
    {
        $student = User::where('id', $studentId)->where('role', 'student')->where('kelas_id', $classId)->first();
        if (! $student) {
            throw new \RuntimeException('Student not found in this class');
        }

        $student->update(['kelas_id' => null]);

        return $student;
    }

    public function getClassStudents(int $classId)
    {
        $class = $this->repository->find($classId);
        if (! $class) {
            throw new \RuntimeException('Class not found');
        }

        return $class->students()->get();
    }
}
