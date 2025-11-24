<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = JobCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('slug', 'ilike', "%{$search}%");
        }

        return Inertia::render('MasterData/JobCategories/Index', [
            'jobCategories' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_job_categories',
        ]);

        JobCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back()->with('success', 'Job category created successfully.');
    }

    public function update(Request $request, JobCategory $jobCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_job_categories,name,' . $jobCategory->id,
        ]);

        $jobCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back()->with('success', 'Job category updated successfully.');
    }

    public function destroy(JobCategory $jobCategory)
    {
        $jobCategory->delete();

        return redirect()->back()->with('success', 'Job category deleted successfully.');
    }
}
