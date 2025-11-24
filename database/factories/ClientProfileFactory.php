<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'industry' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Retail', 'Education', 'Manufacturing']),
            'address' => $this->faker->address(),
            'website' => 'https://' . $this->faker->domainName(),
            'pic_name' => $this->faker->name(),
            'pic_phone' => $this->faker->phoneNumber(),
        ];
    }
}
