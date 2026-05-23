<?php

namespace App\Modules\Academic\Class\Services;

use App\Models\User;
use App\Modules\Academic\Class\Repositories\ClassRepository;

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
        return $this->repository->create($data);
    }

    public function updateClass(int $id, array $data)
    {
        return $this->repository->update($id, $data);
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
