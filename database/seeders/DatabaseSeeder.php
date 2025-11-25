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
            MasterDataSeeder::class,
            MasterDocumentTypeSeeder::class,
            SuperadminSeeder::class,
            ClientProfileSeeder::class,
            CandidateSeeder::class,
            JobSeeder::class,
            ApplicationSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
    }
}
