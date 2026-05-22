<?php

namespace App\Modules\Grade\Repositories;

use App\Modules\Grade\Interfaces\GradeRepositoryInterface;
use App\Modules\Grade\Models\Grade;

class GradeRepository implements GradeRepositoryInterface
{
    public function __construct(protected Grade $model) {}

    public function find(int $id)
    {
        return $this->model->with(['student', 'subject'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $grade = $this->find($id);
        if ($grade) {
            $grade->update($data);
            return $grade;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $grade = $this->find($id);
        if ($grade) {
            return $grade->delete();
        }
        return false;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery()->with(['student', 'subject']);
        $allowed = ['student_id', 'subject_id', 'term'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->latest()->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery()->with(['student', 'subject']);
        $allowed = ['student_id', 'subject_id', 'term'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->latest('id')->paginate($perPage);
    }
}
