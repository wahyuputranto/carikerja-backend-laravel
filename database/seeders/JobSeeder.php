<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
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
        $clientProfileIds = ClientProfile::pluck('id');
        $jobCategoryIds = JobCategory::pluck('id');
        $locationIds = Location::where('type', 'CITY')->pluck('id');

        if ($clientProfileIds->isEmpty() || $jobCategoryIds->isEmpty() || $locationIds->isEmpty()) {
            $this->command->warn('Cannot run JobSeeder because there are no clients, job categories, or city locations.');
            return;
        }

        $this->command->info('Seeding 100 jobs...');

        for ($i = 0; $i < 100; $i++) {
            Job::factory()->create([
                'client_profile_id' => $clientProfileIds->random(),
                'job_category_id' => $jobCategoryIds->random(),
                'location_id' => $locationIds->random(),
            ]);
        }
    }
}
