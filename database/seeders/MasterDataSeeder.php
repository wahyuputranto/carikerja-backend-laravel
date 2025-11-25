<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Job Categories
        $categories = [
            ['name' => 'IT & Software', 'slug' => 'it-software'],
            ['name' => 'Marketing & Sales', 'slug' => 'marketing-sales'],
            ['name' => 'Finance & Accounting', 'slug' => 'finance-accounting'],
            ['name' => 'Human Resources', 'slug' => 'human-resources'],
            ['name' => 'Customer Service', 'slug' => 'customer-service'],
            ['name' => 'Operations & Logistics', 'slug' => 'operations-logistics'],
            ['name' => 'Design & Creative', 'slug' => 'design-creative'],
            ['name' => 'Engineering', 'slug' => 'engineering'],
            ['name' => 'Healthcare', 'slug' => 'healthcare'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Hospitality & Tourism', 'slug' => 'hospitality-tourism'],
            ['name' => 'Agriculture & Plantation', 'slug' => 'agriculture-plantation'],
            ['name' => 'Manufacturing', 'slug' => 'manufacturing'],
            ['name' => 'Construction', 'slug' => 'construction'],
        ];

        DB::table('master_job_categories')->truncate();
        foreach ($categories as $category) {
            DB::table('master_job_categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('master_locations')->truncate();
        // Locations - Indonesia
        $indonesia = DB::table('master_locations')->insertGetId([
            'parent_id' => null,
            'name' => 'Indonesia',
            'type' => 'COUNTRY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provincesID = [
            'DKI Jakarta' => ['Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Utara'],
            'Jawa Barat' => ['Bandung', 'Bekasi', 'Bogor', 'Depok'],
            'Jawa Timur' => ['Surabaya', 'Malang', 'Sidoarjo'],
            'Jawa Tengah' => ['Semarang', 'Solo', 'Yogyakarta'],
            'Banten' => ['Tangerang', 'Tangerang Selatan', 'Serang'],
        ];

        foreach ($provincesID as $provinceName => $cities) {
            $provinceId = DB::table('master_locations')->insertGetId(['parent_id' => $indonesia, 'name' => $provinceName, 'type' => 'PROVINCE', 'created_at' => now(), 'updated_at' => now()]);
            foreach ($cities as $cityName) {
                DB::table('master_locations')->insert(['parent_id' => $provinceId, 'name' => $cityName, 'type' => 'CITY', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Locations - Turkey
        $turkey = DB::table('master_locations')->insertGetId([
            'parent_id' => null,
            'name' => 'Turkey',
            'type' => 'COUNTRY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provincesTR = [
            'Istanbul' => ['Fatih', 'Beyoğlu', 'Kadıköy'],
            'Ankara' => ['Çankaya', 'Kızılay', 'Ulus'],
            'Izmir' => ['Konak', 'Bornova', 'Karşıyaka'],
        ];

        foreach ($provincesTR as $provinceName => $cities) {
            $provinceId = DB::table('master_locations')->insertGetId(['parent_id' => $turkey, 'name' => $provinceName, 'type' => 'PROVINCE', 'created_at' => now(), 'updated_at' => now()]);
            foreach ($cities as $cityName) {
                DB::table('master_locations')->insert(['parent_id' => $provinceId, 'name' => $cityName, 'type' => 'CITY', 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        
        // Locations - Germany
        $germany = DB::table('master_locations')->insertGetId([
            'parent_id' => null,
            'name' => 'Germany',
            'type' => 'COUNTRY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provincesDE = [
            'Berlin' => ['Mitte', 'Charlottenburg', 'Kreuzberg'],
            'Bavaria' => ['Munich', 'Nuremberg'],
            'Hesse' => ['Frankfurt', 'Wiesbaden'],
        ];

        foreach ($provincesDE as $provinceName => $cities) {
            $provinceId = DB::table('master_locations')->insertGetId(['parent_id' => $germany, 'name' => $provinceName, 'type' => 'PROVINCE', 'created_at' => now(), 'updated_at' => now()]);
            foreach ($cities as $cityName) {
                DB::table('master_locations')->insert(['parent_id' => $provinceId, 'name' => $cityName, 'type' => 'CITY', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Skills
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React', 'Node.js',
            'Python', 'Java', 'C++', 'SQL', 'MongoDB', 'PostgreSQL',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Git', 'Agile',
            'Project Management', 'Communication', 'Leadership', 'Problem Solving',
            'Hotel Management', 'F&B Service', 'Housekeeping', 'Front Office',
            'Crop Cultivation', 'Soil Management', 'Pest Control', 'Harvesting',
            'Palm Oil Processing', 'Rubber Tapping', 'Estate Management',
        ];

        DB::table('master_skills')->truncate();
        foreach ($skills as $skill) {
            DB::table('master_skills')->insert([
                'name' => $skill,
                'slug' => \Illuminate\Support\Str::slug($skill),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
