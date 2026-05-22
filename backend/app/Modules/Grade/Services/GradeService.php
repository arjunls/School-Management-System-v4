<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Grade\Interfaces\GradeRepositoryInterface;
use App\Modules\Subject\Models\Subject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GradeService
{
    public function __construct(protected GradeRepositoryInterface $repository) {}

    public function getGrade(int $id)
    {
        return $this->repository->find($id);
    }

    public function getAllGrades(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getGradesPaginated(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function createGrade(array $data)
    {
        $validator = Validator::make($data, [
            'student_id' => 'required|integer|exists:users,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:2',
            'term' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $grade = $this->repository->create($validator->validated());
        $this->notifyStudent($grade);
        return $grade;
    }

    public function updateGrade(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'student_id' => 'sometimes|required|integer|exists:users,id',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'score' => 'sometimes|nullable|numeric|min:0|max:100',
            'grade' => 'sometimes|nullable|string|max:2',
            'term' => 'sometimes|nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $grade = $this->repository->update($id, $validator->validated());
        if ($grade) $this->notifyStudent($grade);
        return $grade;
    }

    public function deleteGrade(int $id): bool
    {
        return $this->repository->delete($id);
    }

    protected function notifyStudent($grade): void
    {
        try {
            $student = User::find($grade->student_id);
            $subject = Subject::find($grade->subject_id);
            if ($student && $subject) {
                $student->notify(new \App\Notifications\GradeAssigned(
                    $subject->name,
                    $grade->score,
                    $grade->grade
                ));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send grade notification', ['error' => $e->getMessage()]);
        }
    }
}
