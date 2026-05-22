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
            Permission::create(['name' => $permission]);
        }

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $teacher = Role::create(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'view-students', 'view-classes',
            'view-subjects', 'view-schedules',
            'view-grades', 'create-grades', 'edit-grades',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'export-data',
            'view-dashboard',
        ]);

        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo([
            'view-grades', 'view-schedules', 'view-attendance', 'view-dashboard',
        ]);

        $parent = Role::create(['name' => 'parent']);
        $parent->givePermissionTo([
            'view-grades', 'view-schedules', 'view-attendance', 'view-dashboard',
        ]);
    }
}
