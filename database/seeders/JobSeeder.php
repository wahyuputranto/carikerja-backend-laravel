<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientProfileIds = Client::pluck('id');
        $jobCategoryIds = JobCategory::pluck('id');
        $jobLocationIds = JobLocation::where('is_active', true)->pluck('id');

        if ($clientProfileIds->isEmpty() || $jobCategoryIds->isEmpty() || $jobLocationIds->isEmpty()) {
            $this->command->warn('Cannot run JobSeeder because there are no clients, job categories, or job locations.');
            return;
        }

        $targetCategories = [
            'customer-service',
            'operations-logistics',
            'Food & Beverage',
            'Housekeeping',
            'Security'
        ];

        $this->command->info('Seeding jobs for specific categories...');

        foreach ($targetCategories as $slug) {
            $category = JobCategory::where('slug', $slug)->first();
            
            if (!$category) {
                $this->command->warn("Category {$slug} not found.");
                continue;
            }

            $this->command->info("Creating 5 jobs for {$category->name}...");

            for ($i = 0; $i < 5; $i++) {
                Job::factory()->create([
                    'client_profile_id' => $clientProfileIds->random(),
                    'job_category_id' => $category->id,
                    'job_location_id' => $jobLocationIds->random(),
                ]);
            }
        }
    }
}

