<?php

namespace App\Modules\AcademicYear\Repositories;

use App\Modules\AcademicYear\Models\AcademicYear;

class AcademicYearRepository
{
    public function __construct(protected AcademicYear $model) {}

    public function getAll(array $filters = [])
    {
        $query = $this->model->with('terms');
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('start_date', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->with('terms');
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->with('terms')->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    public function delete(int $id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    public function getActive()
    {
        return $this->model->active()->with('terms')->first();
    }
}
