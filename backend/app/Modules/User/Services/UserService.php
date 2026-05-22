<?php

namespace App\Modules\User\Services;

use App\Modules\User\Interfaces\UserRepositoryInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getUser($id)
    {
        return $this->repository->find($id);
    }

    public function getUserByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function createUser(array $data)
    {
        // Validate data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,teacher,student,parent,staff',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Only use validated fields to avoid mass assignment of unexpected attributes
        $data = $validator->validated();

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'student';
        }

        return $this->repository->create($data);
    }

    public function updateUser($id, array $data)
    {
        // Validate data
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|string|in:admin,teacher,student,parent,staff',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Only use validated fields to avoid mass assignment of unexpected attributes
        $data = $validator->validated();

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->repository->delete($id);
    }

    public function getAllUsers($filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getUsersPaginated($perPage = 15, $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $user = $this->repository->find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $user;
    }
}
