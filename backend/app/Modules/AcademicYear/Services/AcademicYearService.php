<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Repositories\AcademicYearRepository;
use App\Modules\AcademicYear\Repositories\TermRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        if (!empty($validated['is_active'])) {
            $this->academicYearRepo->getAll()->each->update(['is_active' => false]);
        }

        return $this->academicYearRepo->create($validated);
    }

    public function update(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'string|max:50',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        if (!empty($validated['is_active'])) {
            $this->academicYearRepo->getAll()->each->update(['is_active' => false]);
        }

        return $this->academicYearRepo->update($id, $validated);
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
        $validator = Validator::make($data, [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        if (!empty($validated['is_active'])) {
            $this->termRepo->getAll(['academic_year_id' => $validated['academic_year_id']])->each->update(['is_active' => false]);
        }

        return $this->termRepo->create($validated);
    }

    public function updateTerm(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'academic_year_id' => 'exists:academic_years,id',
            'name' => 'string|max:50',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        if (!empty($validated['is_active'])) {
            $academicYearId = $validated['academic_year_id'] ?? $this->termRepo->find($id)?->academic_year_id;
            if ($academicYearId) {
                $this->termRepo->getAll(['academic_year_id' => $academicYearId])->each->update(['is_active' => false]);
            }
        }

        return $this->termRepo->update($id, $validated);
    }

    public function deleteTerm(int $id)
    {
        return $this->termRepo->delete($id);
    }
}
