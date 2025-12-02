<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Client company access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Vendor',
                'slug' => 'vendor',
                'description' => 'Vendor company access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('roles')->insert($roles);
    }
}
