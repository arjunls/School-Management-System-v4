<?php

namespace App\Modules\Subject\Services;

use App\Modules\Subject\Interfaces\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:20',
            'teacher_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->create($validator->validated());
    }

    public function updateSubject(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:100',
            'code' => 'sometimes|required|string|max:20|unique:subjects,code,' . $id,
            'description' => 'sometimes|nullable|string',
            'credits' => 'sometimes|nullable|integer|min:1|max:20',
            'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $this->repository->update($id, $validator->validated());
    }

    public function deleteSubject(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
