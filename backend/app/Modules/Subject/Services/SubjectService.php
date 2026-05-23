<?php

namespace App\Modules\Subject\Services;

use App\Modules\Subject\Interfaces\SubjectRepositoryInterface;

class SubjectService
{
    public function __construct(protected SubjectRepositoryInterface $repository) {}

    public function getSubject(int $id)
    {
        return $this->repository->find($id);
    }

    public function getAllSubjects(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getSubjectsPaginated(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function createSubject(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateSubject(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSubject(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
