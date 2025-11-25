<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to start fresh
        DB::statement('TRUNCATE TABLE job_applications CASCADE');

        $jobs = Job::all();
        $candidates = Candidate::all();

        if ($jobs->isEmpty() || $candidates->isEmpty()) {
            $this->command->warn('No jobs or candidates found. Skipping application seeding.');
            return;
        }

        $statuses = [
            'APPLIED' => [1],
            'INTERVIEW' => [2, 3, 4, 5],
            'OFFERING' => [6],
            'PROCESSING_VISA' => [7, 8, 9, 10],
            'DEPLOYED' => [11, 12],
            'REJECTED' => [1, 2, 3], // Rejected at early stages usually
        ];

        $this->command->info('Seeding applications...');

        foreach ($candidates as $candidate) {
            // Each candidate applies to 1-3 jobs
            $randomJobs = $jobs->random(rand(1, 3));

            foreach ($randomJobs as $job) {
                $status = array_rand($statuses);
                $possibleSteps = $statuses[$status];
                $step = $possibleSteps[array_rand($possibleSteps)];

                Application::create([
                    'job_id' => $job->id,
                    'candidate_id' => $candidate->id,
                    'status' => $status,
                    'current_step' => $step,
                    'applied_at' => now()->subDays(rand(1, 60)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Applications seeded successfully.');
    }
}
