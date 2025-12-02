<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Seed Pre-Interviews (Candidate Level)
        $candidates = Candidate::all();
        $interviewer = User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->first();

        if (!$interviewer) {
            $interviewer = User::first();
        }

        foreach ($candidates as $candidate) {
            // 30% chance to have a Pre-Interview
            if ($faker->boolean(30)) {
                $status = $faker->randomElement(['SCHEDULED', 'PASSED', 'FAILED']);
                
                $scheduledAt = $faker->dateTimeBetween('-1 month', '+1 month');
                $result = null;
                $feedback = null;

                if ($status !== 'SCHEDULED') {
                    $scheduledAt = $faker->dateTimeBetween('-1 month', 'now');
                    $result = $status;
                    $feedback = $faker->sentence;
                }

                Interview::create([
                    'candidate_id' => $candidate->id,
                    'interviewer_id' => $interviewer->id,
                    'stage' => 'PRE_INTERVIEW',
                    'scheduled_at' => $scheduledAt,
                    'type' => $faker->randomElement(['ONLINE', 'OFFLINE']),
                    'meeting_link' => 'https://meet.google.com/' . $faker->slug,
                    'location_address' => $faker->address,
                    'result' => $result,
                    'feedback_notes' => $feedback,
                ]);
            }
        }

        // 2. Seed User Interviews (Application Level)
        $applications = Application::whereIn('status', ['INTERVIEW', 'OFFERING', 'HIRED'])->get();

        foreach ($applications as $app) {
            Interview::create([
                'application_id' => $app->id,
                'candidate_id' => $app->candidate_id,
                'interviewer_id' => $interviewer->id,
                'stage' => 'USER_INTERVIEW',
                'scheduled_at' => $app->interview_date ?? $faker->dateTimeBetween('-1 month', 'now'),
                'type' => $app->interview_location == 'Online' ? 'ONLINE' : 'OFFLINE',
                'meeting_link' => $app->interview_location == 'Online' ? 'https://meet.google.com/xyz-abc' : null,
                'location_address' => $app->interview_address,
                'result' => $app->status == 'REJECTED' ? 'FAILED' : 'PASSED',
                'feedback_notes' => $app->interview_notes,
            ]);
        }
    }
}
