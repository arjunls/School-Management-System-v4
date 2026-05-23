<?php

namespace App\Modules\Student\Services;

use App\Modules\Student\Interfaces\StudentRepositoryInterface;
use App\Modules\Student\Repositories\StudentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    protected $repository;

    public function __construct(StudentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getStudent($id)
    {
        return $this->repository->find($id);
    }

    public function getStudentByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function createStudent(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Set default role as student
        $data['role'] = 'student';

        // Only admins can explicitly set status; others default to active
        $currentUser = Auth::user();
        if (! $currentUser || $currentUser->role !== 'admin') {
            unset($data['status']);
        }

        $data['status'] = $data['status'] ?? 'active';

        return $this->repository->create($data);
    }

    public function updateStudent($id, array $data)
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Only admins can change status
        $currentUser = Auth::user();
        if (! $currentUser || $currentUser->role !== 'admin') {
            unset($data['status']);
        }

        return $this->repository->update($id, $data);
    }

    public function deleteStudent($id)
    {
        return $this->repository->delete($id);
    }

    public function getAllStudents($filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getStudentsPaginated($perPage = 15, $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }
}
