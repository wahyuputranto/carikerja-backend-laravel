<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleJobSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have a client
        $client = Client::first();
        if (!$client) {
            $client = Client::factory()->create([
                'name' => 'Sample Client',
                'email' => 'client@sample.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Get Categories
        $hospitality = JobCategory::where('slug', 'hospitality-tourism')->first();
        $fnb = JobCategory::where('slug', 'food-beverage')->first();

        if (!$hospitality || !$fnb) {
            $this->command->error("Categories not found! Please run MasterDataSeeder first.");
            return;
        }

        // 3. Get generic location
        $locations = JobLocation::inRandomOrder()->limit(10)->get();
        if ($locations->isEmpty()) {
            $this->command->error("Locations not found! Please run JobLocationSeeder first.");
            return;
        }

        // 4. Create Hospitality Jobs
        $hospitalityTitles = [
            'Front Office Manager',
            'Housekeeping Supervisor',
            'Concierge Staff',
            'Hotel Receptionist',
            'Guest Relations Officer'
        ];

        foreach ($hospitalityTitles as $index => $title) {
            $loc = $locations[$index] ?? $locations->first();
            Job::create([
                'id' => Str::uuid(),
                'client_profile_id' => $client->id,
                'job_category_id' => $hospitality->id,
                'job_location_id' => $loc->id,
                'title' => ucwords(strtolower($title)),
                'slug' => Str::slug($title . '-' . uniqid()),
                'status' => 'PUBLISHED',
                'description' => "We are looking for a $title to join our team. \n\nResponsibilities:\n- Assist guests\n- Maintain high standards",
                'requirements' => "- 2 years experience\n- English proficiency\n- Friendly attitude",
                'salary_min' => 500,
                'salary_max' => 1500,
                'quota' => 5,
                'deadline' => now()->addDays(30),
            ]);
        }

        // 5. Create F&B Jobs
        $fnbTitles = [
            'Head Chef',
            'Sous Chef',
            'Bartender',
            'Waiter/Waitress',
            'Restaurant Manager'
        ];

        foreach ($fnbTitles as $index => $title) {
            $loc = $locations[$index + 5] ?? $locations->first();
             Job::create([
                'id' => Str::uuid(),
                'client_profile_id' => $client->id,
                'job_category_id' => $fnb->id,
                'job_location_id' => $loc->id,
                'title' => $title,
                'slug' => Str::slug($title . '-' . uniqid()),
                'status' => 'PUBLISHED',
                'description' => "We are looking for a $title to join our F&B team. \n\nResponsibilities:\n- Ensure food quality\n- Provide excellent service",
                'requirements' => "- 1 year experience\n- Team player\n- Knowledge of food safety",
                'salary_min' => 450,
                'salary_max' => 1200,
                'quota' => 3,
                'deadline' => now()->addDays(30),
            ]);
        }

        $this->command->info('Created 10 sample jobs (5 Hospitality, 5 F&B).');
    }
}
