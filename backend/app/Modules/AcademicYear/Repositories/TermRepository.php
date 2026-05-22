<?php

namespace App\Modules\AcademicYear\Repositories;

use App\Modules\AcademicYear\Models\Term;

class TermRepository
{
    public function __construct(protected Term $model) {}

    public function getAll(array $filters = [])
    {
        $query = $this->model->with('academicYear');
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        return $query->orderBy('start_date')->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->with('academicYear');
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        return $query->orderBy('start_date')->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->with('academicYear')->find($id);
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

    public function getActiveByAcademicYear(int $academicYearId)
    {
        return $this->model->where('academic_year_id', $academicYearId)->active()->first();
    }
}
