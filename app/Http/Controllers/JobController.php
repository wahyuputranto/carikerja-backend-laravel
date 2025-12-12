<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\DocumentType;
use App\Models\Client;
use App\Models\Notification;
use App\Models\ClientBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['jobCategory', 'jobLocation', 'client']);

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

        if ($request->filled('job_location_id')) {
            $query->where('job_location_id', $request->job_location_id);
        }

        if ($request->filled('country')) {
            $query->whereHas('jobLocation', function ($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->job_category_id);
        }

        // If user is a client, only show their jobs
        if (auth()->guard('client')->check()) {
            $query->where('client_profile_id', auth()->guard('client')->id());
        }
        
        $jobs = $query->latest()->paginate(15)->withQueryString();
        $jobLocations = JobLocation::active()->orderBy('country')->orderBy('province')->orderBy('city')->get();
        $categories = JobCategory::orderBy('name')->get(['id', 'name']);

        // Get unique countries for filter
        $countries = JobLocation::active()
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'status', 'job_location_id', 'country', 'job_category_id']),
            'jobLocations' => $jobLocations,
            'countries' => $countries,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $jobLocations = JobLocation::active()
            ->orderBy('country')
            ->orderBy('province')
            ->orderBy('city')
            ->get();
        
        $batches = null;
        if (auth()->guard('client')->check()) {
            $batches = ClientBatch::where('client_id', auth()->guard('client')->id())->where('is_active', true)->get();
        } elseif (auth()->user()?->hasRole('superadmin')) {
             // For superadmin, we might want to fetch batches dynamically via API based on selected client, 
             // but for now let's pass all active batches to let frontend filter or simplified.
             // Accessing ClientBatch directly.
             $batches = ClientBatch::where('is_active', true)->get();
        }

        return Inertia::render('Jobs/Create', [
            'categories' => JobCategory::all(),
            'jobLocations' => $jobLocations,
            'clients' => auth()->user()?->hasRole('superadmin') ? Client::all() : null,
            'batches' => $batches,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'job_category_id' => 'required|exists:master_job_categories,id',
            'job_location_id' => 'required|exists:job_locations,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'deadline' => 'nullable|date|after:today',
            'status' => 'required|in:DRAFT,PUBLISHED,CLOSED',
            'client_profile_id' => auth()->user()?->hasRole('superadmin') ? 'required|exists:clients,id' : 'nullable',
            'client_batch_id' => 'required|exists:client_batches,id',
        ]);
        
        if (auth()->guard('client')->check()) {
            $validated['client_profile_id'] = auth()->guard('client')->id();
        }

        $validated['slug'] = Str::slug($validated['title'] . '-' . uniqid());

        $job = Job::create($validated);
        
        // Notify Client if Admin posted it
        if (auth()->user() && auth()->user()->hasRole('superadmin') && !empty($validated['client_profile_id'])) {
             Notification::create([
                'user_id' => $validated['client_profile_id'],
                'title' => 'New Job Posted by Admin',
                'message' => 'Admin has posted a new job: ' . $job->title,
                'type' => 'job_posted',
                'is_read' => false,
                'related_id' => $job->id,
            ]);
        }

        return redirect()->route('jobs.index')->with('success', 'Job posted successfully.');
    }

    public function edit(Job $job)
    {
        // Policy check: only superadmin or the client who owns the job can edit
        $this->authorize('update', $job);

        $jobLocations = JobLocation::active()
            ->orderBy('country')
            ->orderBy('province')
            ->orderBy('city')
            ->get();
            
        $batches = null;
        if (auth()->guard('client')->check()) {
            $batches = ClientBatch::where('client_id', auth()->guard('client')->id())->where('is_active', true)->get();
        } elseif (auth()->user()?->hasRole('superadmin')) {
             $batches = ClientBatch::where('is_active', true)->get();
        }

        return Inertia::render('Jobs/Edit', [
            'job' => $job->load(['jobCategory', 'jobLocation', 'batch']),
            'categories' => JobCategory::all(),
            'jobLocations' => $jobLocations,
            'clients' => auth()->user()?->hasRole('superadmin') ? Client::all() : null,
            'batches' => $batches,
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
            'job_location_id' => 'required|exists:job_locations,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'deadline' => 'nullable|date',
            'status' => 'required|in:DRAFT,PUBLISHED,CLOSED',
            'client_profile_id' => auth()->user()?->hasRole('superadmin') ? 'required|exists:clients,id' : 'nullable',
            'client_batch_id' => 'required|exists:client_batches,id',
        ]);

        if (auth()->user()?->hasRole('superadmin')) {
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
