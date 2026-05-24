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
            // Dashboard
            'view-dashboard',
            // Siswa
            'view-siswa', 'create-siswa', 'edit-siswa', 'delete-siswa',
            // Guru
            'view-guru', 'create-guru', 'edit-guru', 'delete-guru',
            // Kelas
            'view-kelas', 'create-kelas', 'edit-kelas', 'delete-kelas',
            // Kehadiran
            'view-kehadiran', 'create-kehadiran', 'edit-kehadiran', 'delete-kehadiran',
            // Jadwal
            'view-jadwal', 'create-jadwal', 'edit-jadwal', 'delete-jadwal',
            // Nilai
            'view-nilai', 'create-nilai', 'edit-nilai', 'delete-nilai',
            // Pembayaran
            'view-pembayaran', 'create-pembayaran', 'edit-pembayaran', 'delete-pembayaran',
            // Laporan
            'view-laporan', 'create-laporan', 'edit-laporan', 'delete-laporan',
            // Dokumen
            'view-dokumen', 'create-dokumen', 'edit-dokumen', 'delete-dokumen',
            // Data
            'import-data', 'export-data',
            // Pengaturan
            'manage-pengaturan', 'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Sekolah — all permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Guru
        $teacher = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $teacher->givePermissionTo([
            'view-dashboard',
            'view-siswa', 'create-siswa', 'edit-siswa',
            'view-guru',
            'view-kelas',
            'view-kehadiran', 'create-kehadiran', 'edit-kehadiran',
            'view-jadwal',
            'view-nilai', 'create-nilai', 'edit-nilai',
            'view-pembayaran',
            'view-laporan', 'create-laporan',
            'view-dokumen', 'create-dokumen',
            'export-data',
        ]);

        // Wali Kelas (extends Guru)
        $waliKelas = Role::firstOrCreate(['name' => 'wali-kelas', 'guard_name' => 'web']);
        $waliKelas->givePermissionTo($teacher->permissions);
        $waliKelas->givePermissionTo(['edit-kelas', 'delete-kehadiran', 'delete-nilai']);

        // Siswa
        $student = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $student->givePermissionTo([
            'view-dashboard',
            'view-jadwal',
            'view-nilai',
            'view-kehadiran',
            'view-laporan',
            'view-dokumen',
        ]);

        // Orang Tua
        $parent = Role::firstOrCreate(['name' => 'orang-tua', 'guard_name' => 'web']);
        $parent->givePermissionTo([
            'view-dashboard',
            'view-jadwal',
            'view-nilai',
            'view-kehadiran',
            'view-pembayaran',
            'view-laporan',
        ]);

        // Tata Usaha
        $tataUsaha = Role::firstOrCreate(['name' => 'tata-usaha', 'guard_name' => 'web']);
        $tataUsaha->givePermissionTo(Permission::all());
    }
}
