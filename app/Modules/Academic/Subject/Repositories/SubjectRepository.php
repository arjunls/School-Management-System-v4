<?php

namespace App\Modules\Academic\Subject\Repositories;

use App\Modules\Academic\Subject\Interfaces\SubjectRepositoryInterface;
use App\Modules\Academic\Subject\Models\Subject;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function __construct(protected Subject $model) {}

    public function find(int $id)
    {
        return $this->model->with('teacher')->find($id);
    }

    public function findByCode(string $code)
    {
        return $this->model->with('teacher')->where('code', $code)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $subject = $this->find($id);
        if ($subject) {
            $subject->update($data);
            return $subject;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $subject = $this->find($id);
        if ($subject) {
            return $subject->delete();
        }
        return false;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery()->with('teacher');
        $allowed = ['name', 'code', 'teacher_id', 'credits'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery()->with('teacher');
        $allowed = ['name', 'code', 'teacher_id', 'credits'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('name')->paginate($perPage);
    }
}
