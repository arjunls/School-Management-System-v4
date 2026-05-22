<?php

namespace App\Modules\Teacher\Services;

use App\Modules\Teacher\Interfaces\TeacherRepositoryInterface;
use App\Modules\Teacher\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        // Validate data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'photo' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Use only validated data to avoid mass assignment of unexpected attributes
        $data = $validator->validated();

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
        // Validate data
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'photo' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|in:active,inactive,suspended',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Use only validated data
        $data = $validator->validated();

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
