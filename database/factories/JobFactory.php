<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        $salaryMin = $this->faker->numberBetween(5, 10) * 1000000;
        $salaryMax = $salaryMin + ($this->faker->numberBetween(1, 5) * 1000000);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . uniqid(),
            'description' => $this->faker->realText(800),
            'requirements' => $this->faker->realText(400),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'quota' => $this->faker->numberBetween(1, 5),
            'deadline' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'PUBLISHED', 'PUBLISHED', 'CLOSED']),
        ];
    }
}
