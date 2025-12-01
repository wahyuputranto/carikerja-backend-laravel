<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateProfile>
 */
use App\Models\JobCategory;

// ...

class CandidateProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'about_me' => $this->faker->realText(500),
            'interested_job_category_id' => JobCategory::inRandomOrder()->first()?->id,
        ];
    }
}
