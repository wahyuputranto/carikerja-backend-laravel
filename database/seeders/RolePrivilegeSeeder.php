<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePrivilege;

class RolePrivilegeSeeder extends Seeder
{
    public function run(): void
    {
        // Define all available permissions
        $permissions = [
            'menu.dashboard',
            'menu.job_posting',
            'menu.talent_pool',
            'menu.clients',
            'menu.master_data',
            
            'job.view',
            'job.create',
            'job.edit',
            'job.delete',
            
            'candidate.view',
            'candidate.edit',
            
            'client.view',
            'client.create',
            'client.edit',
            
            'master_data.view',
            'master_data.manage',
        ];

        // Get Superadmin Role
        $superadmin = Role::where('slug', 'superadmin')->first();

        if ($superadmin) {
            // Clear existing privileges for superadmin
            RolePrivilege::where('role_id', $superadmin->id)->delete();

            // Assign ALL permissions to Superadmin
            foreach ($permissions as $permission) {
                RolePrivilege::create([
                    'role_id' => $superadmin->id,
                    'permission' => $permission,
                ]);
            }
        }
        
        // Example: Create a 'Recruiter' role if it doesn't exist and give limited access
        $recruiter = Role::firstOrCreate(
            ['slug' => 'recruiter'],
            ['name' => 'Recruiter', 'description' => 'Can manage jobs and candidates']
        );
        
        // Clear existing privileges for recruiter
        RolePrivilege::where('role_id', $recruiter->id)->delete();
        
        $recruiterPermissions = [
            'menu.dashboard',
            'menu.job_posting',
            'menu.talent_pool',
            'job.view',
            'job.create',
            'job.edit',
            'candidate.view',
            'candidate.edit',
        ];
        
        foreach ($recruiterPermissions as $permission) {
            RolePrivilege::create([
                'role_id' => $recruiter->id,
                'permission' => $permission,
            ]);
        }
    }
}
