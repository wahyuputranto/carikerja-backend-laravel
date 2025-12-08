<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        
        $this->call([
            RoleSeeder::class,
            RolePrivilegeSeeder::class, // Add privileges and Recruiter role
            MasterDataSeeder::class,
            MasterDocumentTypeSeeder::class,
            JobLocationSeeder::class, // Job locations for Turkey, Taiwan, Bulgaria
            SuperadminSeeder::class,
            RecruiterSeeder::class, // Create Recruiter user
            ClientSeeder::class,
            CandidateSeeder::class,
            // JobSeeder::class,
            SampleJobSeeder::class,
            ApplicationSeeder::class,
            InterviewSeeder::class,
            NotificationSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
    }
}
