<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientRole = Role::where('slug', 'client')->first();

        if (!$clientRole) {
            $this->command->warn('Client role not found. Skipping ClientProfileSeeder.');
            return;
        }

        // Create 1 client
        User::factory()->count(1)->create(['role_id' => $clientRole->id])->each(function ($user) {
            ClientProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
