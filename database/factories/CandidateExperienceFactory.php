<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateExperience>
 */
class CandidateExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-10 years', '-1 year');
        $isCurrent = $this->faker->boolean(25); // 25% chance of being a current job

        return [
            'company_name' => $this->faker->company,
            'position' => $this->faker->jobTitle,
            'start_date' => $startDate,
            'end_date' => $isCurrent ? null : $this->faker->dateTimeBetween($startDate, 'now'),
            'is_current' => $isCurrent,
            'description' => $this->faker->realText(200),
        ];
    }
}
