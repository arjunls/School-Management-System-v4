<?php

namespace App\Modules\User\Services;

use App\Modules\User\Interfaces\UserRepositoryInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

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
