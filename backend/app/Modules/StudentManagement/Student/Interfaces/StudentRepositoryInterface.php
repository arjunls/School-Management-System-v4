<?php

namespace App\Modules\StudentManagement\Student\Interfaces;

interface StudentRepositoryInterface
{
    public function find($id);
    public function findByEmail($email);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAll($filters = []);
    public function paginate($perPage = 15, $filters = []);
}