<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['jobCategory', 'location', 'clientProfile'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function create()
    {
        $categories = JobCategory::all();
        $locations = Location::where('type', 'CITY')->with('parent')->get();
        $documentTypes = DocumentType::all();

        return Inertia::render('Jobs/Create', [
            'categories' => $categories,
            'locations' => $locations,
            'documentTypes' => $documentTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'job_category_id' => 'required|exists:master_job_categories,id',
            'location_id' => 'required|exists:master_locations,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'deadline' => 'nullable|date|after:today',
            'status' => 'required|in:DRAFT,PUBLISHED,CLOSED',
        ]);

        // For now, we'll use the first client profile or create a dummy one
        // In production, this should come from the authenticated user's client profile
        $validated['client_profile_id'] = \App\Models\ClientProfile::first()->id ?? null;

        Job::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Job posted successfully.');
    }

    public function edit(Job $job)
    {
        $categories = JobCategory::all();
        $locations = Location::where('type', 'CITY')->with('parent')->get();
        $documentTypes = DocumentType::all();

        return Inertia::render('Jobs/Edit', [
            'job' => $job->load(['jobCategory', 'location']),
            'categories' => $categories,
            'locations' => $locations,
            'documentTypes' => $documentTypes,
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'job_category_id' => 'required|exists:master_job_categories,id',
            'location_id' => 'required|exists:master_locations,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'deadline' => 'nullable|date',
            'status' => 'required|in:DRAFT,PUBLISHED,CLOSED',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }
}
