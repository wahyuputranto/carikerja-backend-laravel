<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
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
        $locationIds = Location::where('type', 'CITY')->pluck('id');

        if ($clientProfileIds->isEmpty() || $jobCategoryIds->isEmpty() || $locationIds->isEmpty()) {
            $this->command->warn('Cannot run JobSeeder because there are no clients, job categories, or city locations.');
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
                    'location_id' => $locationIds->random(),
                ]);
            }
        }
    }
}
