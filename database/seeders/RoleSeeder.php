<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = User::first()->id;

        Role::create([
            'created_by_id' => $id,
            'name'          => 'super-administrator',
            'display_name'  => 'Super Administrator',
        ]);

        Role::create([
            'created_by_id' => $id,
            'name'          => 'administrator',
            'display_name'  => 'Administrator',
        ]);
    }
}
