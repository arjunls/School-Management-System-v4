<?php

namespace App\Modules\StaffManagement\Teacher\Interfaces;

interface TeacherRepositoryInterface
{
    public function find($id);
    public function findByEmail($email);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAll($filters = []);
    public function paginate($perPage = 15, $filters = []);
}