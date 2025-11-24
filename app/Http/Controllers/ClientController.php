<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clientRole = Role::where('slug', 'client')->first();

        $query = User::where('role_id', $clientRole?->id)
            ->with('clientProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhereHas('clientProfile', function ($q2) use ($search) {
                      $q2->where('company_name', 'ilike', "%{$search}%")
                         ->orWhere('pic_name', 'ilike', "%{$search}%");
                  });
            });
        }

        $clients = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
        ]);

        $clientRole = Role::where('slug', 'client')->firstOrFail();

        DB::transaction(function () use ($request, $clientRole) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $clientRole->id,
            ]);

            $user->clientProfile()->create([
                'company_name' => $request->company_name,
                'industry' => $request->industry,
                'address' => $request->address,
                'website' => $request->website,
                'pic_name' => $request->pic_name,
                'pic_phone' => $request->pic_phone,
            ]);
        });

        return redirect()->back()->with('success', 'Client created successfully.');
    }

    public function update(Request $request, User $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $client->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $client) {
            $client->update($request->only('name', 'email'));

            if ($request->filled('password')) {
                $client->update(['password' => Hash::make($request->password)]);
            }

            $client->clientProfile()->update($request->only(
                'company_name',
                'industry',
                'address',
                'website',
                'pic_name',
                'pic_phone'
            ));
        });

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    public function destroy(User $client)
    {
        $client->delete();

        return redirect()->back()->with('success', 'Client deleted successfully.');
    }
}
