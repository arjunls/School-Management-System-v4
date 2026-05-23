<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Repositories\AcademicYearRepository;
use App\Modules\AcademicYear\Repositories\TermRepository;

class AcademicYearService
{
    public function __construct(
        protected AcademicYearRepository $academicYearRepo,
        protected TermRepository $termRepo,
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->academicYearRepo->getAll($filters);
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->academicYearRepo->paginate($perPage, $filters);
    }

    public function find(int $id)
    {
        return $this->academicYearRepo->find($id);
    }

    public function create(array $data)
    {
        if (!empty($data['is_active'])) {
            $this->academicYearRepo->getAll()->each->update(['is_active' => false]);
        }

        return $this->academicYearRepo->create($data);
    }

    public function update(int $id, array $data)
    {
        if (!empty($data['is_active'])) {
            $this->academicYearRepo->getAll()->each->update(['is_active' => false]);
        }

        return $this->academicYearRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->academicYearRepo->delete($id);
    }

    public function getActive()
    {
        return $this->academicYearRepo->getActive();
    }

    // Terms
    public function getTerms(int $academicYearId)
    {
        return $this->termRepo->getAll(['academic_year_id' => $academicYearId]);
    }

    public function createTerm(array $data)
    {
        if (!empty($data['is_active'])) {
            $this->termRepo->getAll(['academic_year_id' => $data['academic_year_id']])->each->update(['is_active' => false]);
        }

        return $this->termRepo->create($data);
    }

    public function updateTerm(int $id, array $data)
    {
        if (!empty($data['is_active'])) {
            $academicYearId = $data['academic_year_id'] ?? $this->termRepo->find($id)?->academic_year_id;
            if ($academicYearId) {
                $this->termRepo->getAll(['academic_year_id' => $academicYearId])->each->update(['is_active' => false]);
            }
        }

        return $this->termRepo->update($id, $data);
    }

    public function deleteTerm(int $id)
    {
        return $this->termRepo->delete($id);
    }
}
