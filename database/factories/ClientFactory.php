<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
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
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'company_name' => $this->faker->company(),
            'industry' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Retail', 'Education', 'Manufacturing', 'Hospitality', 'Agriculture', 'Plantation', 'Construction']),
            'address' => $this->faker->address(),
            'website' => 'https://' . $this->faker->domainName(),
            'pic_name' => $this->faker->name(),
            'pic_phone' => $this->faker->phoneNumber(),
        ];
    }
}
