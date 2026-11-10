<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PermissionRole::create([
            'permission_id' => Permission::where('name', 'full-access-and-permssions')->first()->id,
            'role_id'  => Role::where('name', 'super-administrator')->first()->id,
        ]);

        PermissionRole::create([
            'permission_id' => Permission::where('name', 'full-access-and-permssions')->first()->id,
            'role_id'  => Role::where('name', 'administrator')->first()->id,
        ]);
    }
}
