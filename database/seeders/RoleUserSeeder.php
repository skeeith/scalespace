<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleUser::create([
            'role_id' => Role::where('name', 'super-administrator')->first()->id,
            'user_id' => User::where('name', 'Super Administrator')->first()->id,
        ]);

        RoleUser::create([
            'role_id' => Role::where('name', 'administrator')->first()->id,
            'user_id' => User::where('name', 'Administrator')->first()->id,
        ]);

        RoleUser::create([
            'role_id' => Role::where('name', 'super-administrator')->first()->id,
            'user_id' => User::where('name', 'Gether Kestrel Medel')->first()->id,
        ]);
    }
}
