<?php

namespace App\Modules\Subject\Interfaces;

interface SubjectRepositoryInterface
{
    public function find(int $id);
    public function findByCode(string $code);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function getAll(array $filters = []);
    public function paginate(int $perPage = 15, array $filters = []);
}
