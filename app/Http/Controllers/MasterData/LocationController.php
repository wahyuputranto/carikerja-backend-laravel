<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::with('parent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('type', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return Inertia::render('MasterData/Locations/Index', [
            'locations' => $query->latest()->paginate(10)->withQueryString(),
            'parentLocations' => Location::where('type', '!=', 'CITY')->get(),
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:COUNTRY,PROVINCE,CITY',
            'parent_id' => 'nullable|exists:master_locations,id',
        ]);

        Location::create($validated);

        return redirect()->back()->with('success', 'Location created successfully.');
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:COUNTRY,PROVINCE,CITY',
            'parent_id' => 'nullable|exists:master_locations,id',
        ]);

        $location->update($validated);

        return redirect()->back()->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        // To prevent deleting locations that are parents of other locations
        if ($location->children()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a location that has child locations.');
        }
        
        $location->delete();

        return redirect()->back()->with('success', 'Location deleted successfully.');
    }
}
