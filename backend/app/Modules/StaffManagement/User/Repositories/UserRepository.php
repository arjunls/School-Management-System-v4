<?php

namespace App\Modules\StaffManagement\User\Repositories;

use App\Modules\StaffManagement\User\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        $user = $this->find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function getAll($filters = [])
    {
        $query = $this->model->newQuery();
        $allowedFilters = ['name', 'email', 'status'];

        // Apply filters with whitelist to avoid arbitrary field filtering
        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowedFilters, true)) {
                continue;
            }

            if (is_array($value)) {
                // Handle range filters like ['from' => 100, 'to' => 200]
                if (isset($value['from']) && isset($value['to'])) {
                    $query->whereBetween($field, [$value['from'], $value['to']]);
                }
                // Handle IN filters
                elseif (isset($value['in'])) {
                    $query->whereIn($field, $value['in']);
                }
                // Handle NOT IN filters
                elseif (isset($value['not_in'])) {
                    $query->whereNotIn($field, $value['not_in']);
                }
            } else {
                // Handle exact match
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    public function paginate($perPage = 15, $filters = [])
    {
        $query = $this->model->newQuery();
        $allowedFilters = ['name', 'email', 'status'];

        // Apply filters with whitelist to avoid arbitrary field filtering
        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowedFilters, true)) {
                continue;
            }

            if (is_array($value)) {
                // Handle range filters like ['from' => 100, 'to' => 200]
                if (isset($value['from']) && isset($value['to'])) {
                    $query->whereBetween($field, [$value['from'], $value['to']]);
                }
                // Handle IN filters
                elseif (isset($value['in'])) {
                    $query->whereIn($field, $value['in']);
                }
                // Handle NOT IN filters
                elseif (isset($value['not_in'])) {
                    $query->whereNotIn($field, $value['not_in']);
                }
            } else {
                // Handle exact match
                $query->where($field, $value);
            }
        }

        return $query->paginate($perPage);
    }
}
