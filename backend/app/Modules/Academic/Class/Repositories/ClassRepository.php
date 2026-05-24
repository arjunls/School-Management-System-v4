<?php

namespace App\Modules\Academic\Class\Repositories;

use App\Modules\Academic\Class\Models\Kelas;

class ClassRepository
{
    public function __construct(protected Kelas $model) {}

    public function find(int $id)
    {
        return $this->model->with('homeroomTeacher')->withCount('students')->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $kelas = $this->find($id);
        if ($kelas) {
            $kelas->update($data);
            return $kelas;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $kelas = $this->find($id);
        if ($kelas) {
            return $kelas->delete();
        }
        return false;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery()->with('homeroomTeacher')->withCount('students');
        $allowed = ['name', 'grade_level', 'homeroom_teacher_id'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery()->with('homeroomTeacher')->withCount('students');
        $allowed = ['name', 'grade_level', 'homeroom_teacher_id'];

        foreach ($filters as $field => $value) {
            if (! in_array($field, $allowed, true)) continue;
            $query->where($field, $value);
        }

        return $query->orderBy('name')->paginate($perPage);
    }
}
