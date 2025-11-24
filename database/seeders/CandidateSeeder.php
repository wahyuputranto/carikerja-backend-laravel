<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateProfile;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 50 candidates
        Candidate::factory()->count(50)->create()->each(function ($candidate) {
            // Create a profile for each candidate
            CandidateProfile::factory()->create(['candidate_id' => $candidate->id]);

            // Create 1 to 3 education records for each candidate
            CandidateEducation::factory()->count(rand(1, 3))->create(['candidate_id' => $candidate->id]);

            // Create 1 to 4 experience records for each candidate
            CandidateExperience::factory()->count(rand(1, 4))->create(['candidate_id' => $candidate->id]);
        });
    }
}
