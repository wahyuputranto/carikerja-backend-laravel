<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get superadmin role
        $superadminRole = DB::table('roles')->where('slug', 'superadmin')->first();

        if (! $superadminRole) {
            $this->command->error('Superadmin role not found. Please run RoleSeeder first.');
            return;
        }

        // Check if user exists
        $existing = DB::table('users')->where('email', 'admin@zmijobs.com')->first();
        if ($existing) {
            $this->command->info('Superadmin already exists.');
            return;
        }

        DB::table('users')->insert([
            'id' => Str::uuid(),
            'role_id' => $superadminRole->id,
            'name' => 'Super Admin',
            'email' => 'admin@carikerja.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Superadmin user created successfully!');
        $this->command->info('Email: admin@zmijobs.com');
        $this->command->info('Password: password');
    }
}
