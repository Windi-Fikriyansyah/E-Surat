<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar Permission berdasarkan modul
        $permissions = [
            // Modul Surat Masuk
            'surat-masuk.view',
            'surat-masuk.create',
            'surat-masuk.edit',
            'surat-masuk.delete',
            'surat-masuk.disposisi',

            // Modul Surat Keluar
            'surat-keluar.view',
            'surat-keluar.create',
            'surat-keluar.edit',
            'surat-keluar.delete',
            'surat-keluar.cetak',

            // Modul Laporan
            'laporan.view',
            'laporan.cetak',
            'laporan.export',

            // Modul Pengguna
            'users.view',
            'users.manage',

            // Modul Role & Permission
            'roles.view',
            'roles.manage',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // 1. Role Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Role Kepala Sekolah
        $roleKepsek = Role::firstOrCreate(['name' => 'Kepala Sekolah', 'guard_name' => 'web']);
        $roleKepsek->syncPermissions([
            'surat-masuk.view',
            'surat-masuk.disposisi',
            'surat-keluar.view',
            'surat-keluar.cetak',
            'laporan.view',
            'laporan.cetak',
            'laporan.export',
        ]);

        // 3. Role Staff
        $roleStaff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $roleStaff->syncPermissions([
            'surat-masuk.view',
            'surat-masuk.create',
            'surat-masuk.edit',
            'surat-keluar.view',
            'surat-keluar.create',
            'surat-keluar.edit',
            'surat-keluar.cetak',
            'laporan.view',
        ]);

        // Sinkronkan role user yang sudah ada di database
        $users = User::all();
        foreach ($users as $u) {
            if ($u->role && in_array($u->role, ['Admin', 'Kepala Sekolah', 'Staff'])) {
                if (!$u->hasRole($u->role)) {
                    $u->assignRole($u->role);
                }
            }
        }
    }
}

