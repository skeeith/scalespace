<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() == 'production') {
            User::factory()->create([
                'name' => 'Super Administrator',
                'email' => 'super.admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 1,
            ]);

            User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 1,
            ]);

            User::factory()->create([
                'name' => 'Gether Kestrel Medel',
                'email' => 'gether.medel@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 1,
            ]);
        } else {
            User::factory()->create([
                'name' => 'Super Administrator',
                'email' => 'super.admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 0,
            ]);

            User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 0,
            ]);

            User::factory()->create([
                'name' => 'Gether Kestrel Medel',
                'email' => 'gether.medel@gmail.com',
                'email_verified_at' => now(),
                'password' => 'password',
                'security_pin' => '123456',
                'remember_token' => Str::random(10),
                'should_reset_password' => 0,
            ]);
        }
    }
}
