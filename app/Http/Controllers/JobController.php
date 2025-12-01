<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\DocumentType;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['jobCategory', 'location.parent.parent', 'clientProfile.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->job_category_id);
        }

        // If user is a client, only show their jobs
        if (auth()->user()->hasRole('client')) {
            $query->where('client_profile_id', auth()->user()->clientProfile->id);
        }
        
        $jobs = $query->latest()->paginate(15)->withQueryString();
        $locations = Location::where('type', 'CITY')->orderBy('name')->get(['id', 'name']);
        $categories = JobCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'status', 'location_id', 'job_category_id']),
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Jobs/Create', [
            'categories' => JobCategory::all(),
            'locations' => Location::where('type', 'CITY')->with('parent')->get(),
            'clients' => auth()->user()->hasRole('superadmin') ? ClientProfile::with('user')->get() : null,
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
            'client_profile_id' => auth()->user()->hasRole('superadmin') ? 'required|exists:client_profiles,id' : 'nullable',
        ]);
        
        if (auth()->user()->hasRole('client')) {
            $validated['client_profile_id'] = $request->user()->clientProfile->id;
        }

        $validated['slug'] = Str::slug($validated['title'] . '-' . uniqid());

        Job::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Job posted successfully.');
    }

    public function edit(Job $job)
    {
        // Policy check: only superadmin or the client who owns the job can edit
        $this->authorize('update', $job);

        return Inertia::render('Jobs/Edit', [
            'job' => $job->load(['jobCategory', 'location']),
            'categories' => JobCategory::all(),
            'locations' => Location::where('type', 'CITY')->with('parent')->get(),
            'clients' => auth()->user()->hasRole('superadmin') ? ClientProfile::with('user')->get() : null,
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

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
            'client_profile_id' => auth()->user()->hasRole('superadmin') ? 'required|exists:client_profiles,id' : 'nullable',
        ]);

        if (auth()->user()->hasRole('superadmin')) {
            $job->client_profile_id = $validated['client_profile_id'];
        }

        $validated['slug'] = Str::slug($validated['title'] . '-' . $job->id);

        $job->update($validated);

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }
}
