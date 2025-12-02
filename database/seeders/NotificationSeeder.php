<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $candidates = Candidate::all();

        foreach ($candidates as $candidate) {
            // Create 1-3 notifications for each candidate
            for ($i = 0; $i < rand(1, 3); $i++) {
                Notification::create([
                    'user_id' => $candidate->id,
                    'title' => $faker->sentence(3),
                    'message' => $faker->paragraph,
                    'type' => $faker->randomElement(['info', 'success', 'warning', 'error']),
                    'is_read' => $faker->boolean,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }
    }
}
