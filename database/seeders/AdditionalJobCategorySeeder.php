<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdditionalJobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Food & Beverage',
            'Housekeeping',
            'Security',
        ];

        foreach ($categories as $category) {
            DB::table('master_job_categories')->updateOrInsert(
                ['name' => $category],
                [
                    'slug' => Str::slug($category),
                    'description' => 'Jobs related to ' . $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
