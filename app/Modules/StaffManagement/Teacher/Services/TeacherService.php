<?php

namespace App\Modules\StaffManagement\Teacher\Services;

use App\Modules\StaffManagement\Teacher\Interfaces\TeacherRepositoryInterface;
use App\Modules\StaffManagement\Teacher\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    protected $repository;

    public function __construct(TeacherRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getTeacher($id)
    {
        return $this->repository->find($id);
    }

    public function getTeacherByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function createTeacher(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Set default role as teacher
        $data['role'] = 'teacher';

        // Only admins can explicitly set status; others default to active
        $currentUser = Auth::user();
        if (! $currentUser || $currentUser->role !== 'admin') {
            unset($data['status']);
        }

        $data['status'] = $data['status'] ?? 'active';

        return $this->repository->create($data);
    }

    public function updateTeacher($id, array $data)
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

    public function deleteTeacher($id)
    {
        return $this->repository->delete($id);
    }

    public function getAllTeachers($filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getTeachersPaginated($perPage = 15, $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }
}
