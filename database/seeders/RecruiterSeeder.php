<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecruiterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Recruiter role exists
        $recruiterRole = Role::firstOrCreate(
            ['slug' => 'recruiter'],
            ['name' => 'Recruiter', 'description' => 'Can manage jobs and candidates']
        );

        // Create Recruiter User
        User::create([
            'id' => Str::uuid(),
            'role_id' => $recruiterRole->id,
            'name' => 'Recruiter Admin',
            'email' => 'recruiter@carikerja.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Recruiter user created successfully!');
        $this->command->info('Email: recruiter@carikerja.id');
        $this->command->info('Password: password');
    }
}
