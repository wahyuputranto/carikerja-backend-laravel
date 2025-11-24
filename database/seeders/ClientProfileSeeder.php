<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientProfileSeeder extends Seeder
{
    public function run(): void
    {
        $firstUser = DB::table('users')->first();
        
        if ($firstUser) {
            DB::table('client_profiles')->insert([
                'id' => Str::uuid(),
                'user_id' => $firstUser->id,
                'company_name' => 'PT Example Company',
                'industry' => 'Technology',
                'address' => 'Jakarta, Indonesia',
                'website' => 'https://example.com',
                'pic_name' => 'John Doe',
                'pic_phone' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
