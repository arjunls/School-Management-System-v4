<?php

namespace App\Modules\Student\Repositories;

use App\Modules\Student\Interfaces\StudentRepositoryInterface;
use App\Models\User;

class StudentRepository implements StudentRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        // Store base User model; apply student role filter in queries
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->baseQuery()->with('kelas')->where('id', $id)->first();
    }

    public function findByEmail($email)
    {
        return $this->baseQuery()->where('email', $email)->first();
    }

    public function create(array $data)
    {
        // Ensure created records are always students
        $data['role'] = 'student';

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $student = $this->find($id);
        if ($student) {
            $student->update($data);
            return $student;
        }
        return null;
    }

    public function delete($id)
    {
        $student = $this->find($id);
        if ($student) {
            $student->delete();
            return true;
        }
        return false;
    }

    public function getAll($filters = [])
    {
        $query = $this->baseQuery()->with('kelas');
        $allowedFilters = ['name', 'email', 'status', 'kelas_id', 'nisn'];

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
        $query = $this->baseQuery()->with('kelas');
        $allowedFilters = ['name', 'email', 'status', 'kelas_id', 'nisn'];

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

    /**
     * Base query for students (role = student).
     */
    protected function baseQuery()
    {
        return $this->model->newQuery()->where('role', 'student');
    }
}
