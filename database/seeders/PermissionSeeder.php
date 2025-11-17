<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Hapus cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | 1. Buat Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'home',
            'kategori',
            'jenis',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Buat Roles
        |--------------------------------------------------------------------------
        */
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user  = Role::firstOrCreate(['name' => 'user']);

        /*
        |--------------------------------------------------------------------------
        | 3. Assign Permissions ke Role
        |--------------------------------------------------------------------------
        */

        // admin → semua halaman boleh
        $admin->syncPermissions(Permission::all());

        // user → hanya boleh home
        $user->syncPermissions(['home']);

        /*
        |--------------------------------------------------------------------------
        | 4. Assign role ke user pertama (optional)
        |--------------------------------------------------------------------------
        */
        $firstUser = \App\Models\User::find(1);
        if ($firstUser) {
            $firstUser->assignRole('admin');
        }
    }
}
