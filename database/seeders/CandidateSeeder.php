<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $candidates = [
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@example.com',
                'phone' => '081234567801',
                'hiring_status' => 'READY_TO_HIRE',
                'bio' => 'Experienced Full Stack Developer with 5+ years in web development. Proficient in Laravel, Vue.js, and modern web technologies.',
                'educations' => [
                    ['institution' => 'Universitas Indonesia', 'degree' => 'Bachelor', 'field_of_study' => 'Computer Science', 'start_year' => 2015, 'end_year' => 2019, 'grade' => '3.75'],
                ],
                'experiences' => [
                    ['company' => 'Tech Startup Inc', 'position' => 'Senior Developer', 'start_date' => '2020-01-01', 'end_date' => null, 'is_current' => true, 'description' => 'Leading development team for e-commerce platform'],
                    ['company' => 'Digital Agency', 'position' => 'Junior Developer', 'start_date' => '2019-06-01', 'end_date' => '2019-12-31', 'is_current' => false, 'description' => 'Developed client websites using Laravel and Vue.js'],
                ],
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'phone' => '081234567802',
                'hiring_status' => 'READY_TO_HIRE',
                'bio' => 'Creative UI/UX Designer passionate about creating beautiful and functional user interfaces. 3 years of experience in design industry.',
                'educations' => [
                    ['institution' => 'Institut Teknologi Bandung', 'degree' => 'Bachelor', 'field_of_study' => 'Visual Communication Design', 'start_year' => 2017, 'end_year' => 2021, 'grade' => '3.85'],
                ],
                'experiences' => [
                    ['company' => 'Design Studio', 'position' => 'UI/UX Designer', 'start_date' => '2021-08-01', 'end_date' => null, 'is_current' => true, 'description' => 'Designing mobile and web applications for various clients'],
                ],
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'phone' => '081234567803',
                'hiring_status' => 'READY_TO_HIRE',
                'bio' => 'Data Analyst with strong background in statistics and business intelligence. Skilled in Python, SQL, and data visualization tools.',
                'educations' => [
                    ['institution' => 'Universitas Gadjah Mada', 'degree' => 'Master', 'field_of_study' => 'Statistics', 'start_year' => 2019, 'end_year' => 2021, 'grade' => '3.90'],
                    ['institution' => 'Universitas Gadjah Mada', 'degree' => 'Bachelor', 'field_of_study' => 'Mathematics', 'start_year' => 2015, 'end_year' => 2019, 'grade' => '3.70'],
                ],
                'experiences' => [
                    ['company' => 'Finance Corp', 'position' => 'Data Analyst', 'start_date' => '2021-09-01', 'end_date' => null, 'is_current' => true, 'description' => 'Analyzing financial data and creating business intelligence reports'],
                ],
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@example.com',
                'phone' => '081234567804',
                'hiring_status' => 'READY_TO_HIRE',
                'bio' => 'Digital Marketing Specialist with expertise in SEO, SEM, and social media marketing. Proven track record in increasing online visibility.',
                'educations' => [
                    ['institution' => 'Universitas Airlangga', 'degree' => 'Bachelor', 'field_of_study' => 'Marketing', 'start_year' => 2016, 'end_year' => 2020, 'grade' => '3.65'],
                ],
                'experiences' => [
                    ['company' => 'E-commerce Platform', 'position' => 'Digital Marketing Manager', 'start_date' => '2022-01-01', 'end_date' => null, 'is_current' => true, 'description' => 'Managing digital marketing campaigns and SEO strategy'],
                    ['company' => 'Marketing Agency', 'position' => 'Marketing Executive', 'start_date' => '2020-07-01', 'end_date' => '2021-12-31', 'is_current' => false, 'description' => 'Executed social media campaigns for various brands'],
                ],
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@example.com',
                'phone' => '081234567805',
                'hiring_status' => 'READY_TO_HIRE',
                'bio' => 'Mobile App Developer specializing in Flutter and React Native. Built 20+ mobile applications for iOS and Android platforms.',
                'educations' => [
                    ['institution' => 'Institut Teknologi Sepuluh Nopember', 'degree' => 'Bachelor', 'field_of_study' => 'Informatics Engineering', 'start_year' => 2016, 'end_year' => 2020, 'grade' => '3.80'],
                ],
                'experiences' => [
                    ['company' => 'Mobile Dev Studio', 'position' => 'Senior Mobile Developer', 'start_date' => '2021-03-01', 'end_date' => null, 'is_current' => true, 'description' => 'Developing cross-platform mobile applications using Flutter'],
                    ['company' => 'Startup Company', 'position' => 'Mobile Developer', 'start_date' => '2020-08-01', 'end_date' => '2021-02-28', 'is_current' => false, 'description' => 'Built native Android applications'],
                ],
            ],
        ];

        foreach ($candidates as $candidateData) {
            $candidateId = Str::uuid();
            
            // Insert candidate
            DB::table('candidates')->insert([
                'id' => $candidateId,
                'name' => $candidateData['name'],
                'email' => $candidateData['email'],
                'phone' => $candidateData['phone'],
                'password' => Hash::make('password'),
                'hiring_status' => $candidateData['hiring_status'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert profile
            DB::table('candidate_profiles')->insert([
                'id' => Str::uuid(),
                'candidate_id' => $candidateId,
                'about_me' => $candidateData['bio'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert educations
            foreach ($candidateData['educations'] as $education) {
                DB::table('candidate_educations')->insert([
                    'id' => Str::uuid(),
                    'candidate_id' => $candidateId,
                    'institution_name' => $education['institution'],
                    'degree' => $education['degree'],
                    'major' => $education['field_of_study'],
                    'start_year' => $education['start_year'],
                    'end_year' => $education['end_year'],
                    'is_current' => false,
                    'gpa' => $education['grade'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert experiences
            foreach ($candidateData['experiences'] as $experience) {
                DB::table('candidate_experiences')->insert([
                    'id' => Str::uuid(),
                    'candidate_id' => $candidateId,
                    'company_name' => $experience['company'],
                    'position' => $experience['position'],
                    'start_date' => $experience['start_date'],
                    'end_date' => $experience['end_date'],
                    'is_current' => $experience['is_current'],
                    'description' => $experience['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
