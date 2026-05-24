<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view-students', 'create-students', 'edit-students', 'delete-students',
            'view-teachers', 'create-teachers', 'edit-teachers', 'delete-teachers',
            'view-classes', 'create-classes', 'edit-classes', 'delete-classes',
            'view-subjects', 'create-subjects', 'edit-subjects', 'delete-subjects',
            'view-schedules', 'create-schedules', 'edit-schedules', 'delete-schedules',
            'view-grades', 'create-grades', 'edit-grades', 'delete-grades',
            'view-attendance', 'create-attendance', 'edit-attendance', 'delete-attendance',
            'import-data', 'export-data',
            'view-dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->givePermissionTo([
            'view-students', 'view-classes',
            'view-subjects', 'view-schedules',
            'view-grades', 'create-grades', 'edit-grades',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'export-data',
            'view-dashboard',
        ]);

        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student->givePermissionTo([
            'view-grades', 'view-schedules', 'view-attendance', 'view-dashboard',
        ]);

        $parent = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $parent->givePermissionTo([
            'view-grades', 'view-schedules', 'view-attendance', 'view-dashboard',
        ]);
    }
}
