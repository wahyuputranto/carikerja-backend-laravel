<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'hiring_status' => $this->faker->randomElement(['AVAILABLE', 'READY_TO_HIRE', 'NOT_AVAILABLE']),
            'last_login_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}
