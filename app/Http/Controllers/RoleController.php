<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePrivilege;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('privileges')->get();
        
        // Group permissions for UI
        $availablePermissions = [
            'Menu Access' => [
                'menu.dashboard',
                'menu.job_posting',
                'menu.talent_pool',
                'menu.clients',
                'menu.master_data',
            ],
            'Job Management' => [
                'job.view',
                'job.create',
                'job.edit',
                'job.delete',
            ],
            'Candidate Management' => [
                'candidate.view',
                'candidate.edit',
            ],
            'Client Management' => [
                'client.view',
                'client.create',
                'client.edit',
            ],
            'Master Data' => [
                'master_data.view',
                'master_data.manage',
            ],
        ];

        return Inertia::render('MasterData/Roles/Index', [
            'roles' => $roles,
            'availablePermissions' => $availablePermissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        if (!empty($validated['permissions'])) {
            foreach ($validated['permissions'] as $permission) {
                RolePrivilege::create([
                    'role_id' => $role->id,
                    'permission' => $permission,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->slug === 'superadmin') {
            return redirect()->back()->with('error', 'Cannot edit Super Admin role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $validated['name'],
            // Don't update slug to avoid breaking references
        ]);

        // Sync permissions
        $role->privileges()->delete();
        
        if (!empty($validated['permissions'])) {
            foreach ($validated['permissions'] as $permission) {
                RolePrivilege::create([
                    'role_id' => $role->id,
                    'permission' => $permission,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'superadmin') {
            return redirect()->back()->with('error', 'Cannot delete Super Admin role.');
        }
        
        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete role because it is assigned to users.');
        }

        $role->delete();
        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
