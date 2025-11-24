<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get superadmin role
        $superadminRole = \DB::table('roles')->where('slug', 'superadmin')->first();

        if (! $superadminRole) {
            $this->command->error('Superadmin role not found. Please run RoleSeeder first.');

            return;
        }

        \DB::table('users')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'role_id' => $superadminRole->id,
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => \Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Superadmin user created successfully!');
        $this->command->info('Email: admin@gmail.com');
        $this->command->info('Password: password');
    }
}
