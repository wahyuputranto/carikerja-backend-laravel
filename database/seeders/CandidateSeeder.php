<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateEmergencyContact;
use App\Models\CandidateLanguage;
use App\Models\CandidateNonFormalEducation;
use App\Models\CandidatePassport;
use App\Models\CandidatePersonalDetail;
use App\Models\CandidateProfile;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ensure we have some job categories
        if (JobCategory::count() === 0) {
            $categories = ['IT', 'Hospitality', 'Construction', 'Healthcare', 'Engineering'];
            foreach ($categories as $cat) {
                JobCategory::create([
                    'name' => $cat,
                    'slug' => \Illuminate\Support\Str::slug($cat),
                ]);
            }
        }

        $jobCategories = JobCategory::all();

        for ($i = 0; $i < 20; $i++) {
            try {
                // 1. Create Candidate
                $candidate = Candidate::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->unique()->phoneNumber,
                    'password' => Hash::make('password'), // Default password
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'interested_job_category_id' => $jobCategories->random()->id,
                ]);

                // 2. Create Candidate Profile
                CandidateProfile::create([
                    'candidate_id' => $candidate->id,
                    'nik' => $faker->nik,
                    'birth_date' => $faker->date('Y-m-d', '-20 years'),
                    'birth_place' => $faker->city,
                    'gender' => $faker->randomElement(['M', 'F']),
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'province' => $faker->state,
                    'postal_code' => $faker->postcode,
                ]);

                // 3. Create Personal Details
                CandidatePersonalDetail::create([
                    'candidate_id' => $candidate->id,
                    'fathers_name' => $faker->name('male'),
                    'mothers_name' => $faker->name('female'),
                    'height' => $faker->numberBetween(150, 190),
                    'weight' => $faker->numberBetween(45, 90),
                    'marital_status' => $faker->randomElement(['Single', 'Married', 'Divorced']),
                    'citizenship' => 'IDN',
                    'religion' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    // 'computer_skills' removed as it is not in the schema
                ]);

                // 4. Create Passport (Randomly for some candidates)
                if ($faker->boolean(70)) {
                    CandidatePassport::create([
                        'candidate_id' => $candidate->id,
                        'passport_number' => strtoupper($faker->bothify('?########')),
                        'issue_date' => $faker->date('Y-m-d', '-2 years'),
                        'issued_by' => 'Kanim ' . $faker->city,
                        'expiry_date' => $faker->date('Y-m-d', '+3 years'),
                    ]);
                }

                // 5. Create Emergency Contact
                CandidateEmergencyContact::create([
                    'candidate_id' => $candidate->id,
                    'name' => $faker->name,
                    'contact_number' => $faker->phoneNumber,
                    'relation' => $faker->randomElement(['Parent', 'Spouse', 'Sibling', 'Friend']),
                    'address' => $faker->address,
                ]);

                // 6. Create Non-Formal Education (0-2 records)
                $numNonFormal = $faker->numberBetween(0, 2);
                for ($j = 0; $j < $numNonFormal; $j++) {
                    CandidateNonFormalEducation::create([
                        'candidate_id' => $candidate->id,
                        'year' => $faker->year,
                        'name' => $faker->words(3, true) . ' Course',
                        'subject' => $faker->word,
                        'country' => 'IDN',
                    ]);
                }

                // 7. Create Languages (1-3 records)
                $languages = ['English', 'Mandarin', 'Arabic', 'Japanese', 'Korean'];
                $numLanguages = $faker->numberBetween(1, 3);
                $selectedLangs = $faker->randomElements($languages, $numLanguages);
                
                foreach ($selectedLangs as $lang) {
                    CandidateLanguage::create([
                        'candidate_id' => $candidate->id,
                        'language' => $lang,
                        'speaking' => $faker->randomElement(['Fair', 'Good', 'Fluent']),
                        'reading' => $faker->randomElement(['Fair', 'Good', 'Fluent']),
                        'writing' => $faker->randomElement(['Fair', 'Good', 'Fluent']),
                    ]);
                }
            } catch (\Exception $e) {
                $this->command->error('Error creating candidate: ' . $e->getMessage());
                $this->command->error($e->getTraceAsString());
            }
        }
    }
}
