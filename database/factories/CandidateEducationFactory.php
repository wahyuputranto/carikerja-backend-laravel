<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateEducation>
 */
class CandidateEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2010, 2020);
        $endYear = $startYear + $this->faker->numberBetween(3, 5);

        return [
            'institution_name' => 'Universitas ' . $this->faker->city,
            'degree' => $this->faker->randomElement(['Bachelor', 'Master', 'PhD']),
            'major' => $this->faker->randomElement(['Computer Science', 'Information Technology', 'Business Administration', 'Marketing', 'Psychology', 'Engineering']),
            'start_year' => $startYear,
            'end_year' => $endYear,
            'is_current' => false,
            'gpa' => $this->faker->randomFloat(2, 3.0, 4.0),
        ];
    }
}
