<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TalentPoolController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['profile', 'educations', 'experiences', 'applications.job']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        // Filter by hiring status
        if ($request->filled('status')) {
            $query->where('hiring_status', $request->status);
        }

        // Filter by position (through applications)
        if ($request->filled('position')) {
            $query->whereHas('applications', function ($q) use ($request) {
                $q->where('job_id', $request->position);
            });
        }

        // Filter by location (through profile city)
        if ($request->filled('location')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('city', 'ilike', "%{$request->location}%");
            });
        }

        $candidates = $query->latest()->paginate(15)->withQueryString();

        // Generate temporary URLs for profile photos
        $candidates->getCollection()->transform(function ($candidate) {
            if ($candidate->profile && $candidate->profile->photo_url) {
                try {
                    $candidate->profile->photo_url = \Storage::disk('minio')->temporaryUrl(
                        $candidate->profile->photo_url,
                        now()->addMinutes(60)
                    );
                } catch (\Exception $e) {
                    // Log error or keep original URL
                }
            }
            return $candidate;
        });

        // Get filter options
        $jobs = Job::select('id', 'title')->where('status', 'OPEN')->get();
        $statuses = [
            ['value' => 'AVAILABLE', 'label' => 'Available'],
            ['value' => 'READY_TO_HIRE', 'label' => 'Ready to Hire'],
            ['value' => 'HIRED', 'label' => 'Hired'],
            ['value' => 'NOT_AVAILABLE', 'label' => 'Not Available'],
        ];

        return Inertia::render('TalentPool/Index', [
            'candidates' => $candidates,
            'filters' => $request->only(['search', 'status', 'position', 'location']),
            'filterOptions' => [
                'jobs' => $jobs,
                'statuses' => $statuses,
            ],
        ]);
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'profile',
            'educations',
            'experiences',
            'documents.documentType',
            'applications.job.clientProfile',
            'applications.job.location.parent.parent',
            'applications.deployment', // Load deployment details
        ]);

        if ($candidate->profile && $candidate->profile->photo_url) {
            try {
                // Generate temporary URL for the photo
                $candidate->profile->photo_url = \Storage::disk('minio')->temporaryUrl(
                    $candidate->profile->photo_url,
                    now()->addMinutes(60)
                );
            } catch (\Exception $e) {
                // Log error or keep original URL if generation fails
                \Log::error('Failed to generate photo URL: ' . $e->getMessage());
            }
        }

        return Inertia::render('TalentPool/Show', [
            'candidate' => $candidate,
        ]);
    }

    public function updateStatus(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'hiring_status' => 'required|in:AVAILABLE,READY_TO_HIRE,HIRED,NOT_AVAILABLE',
        ]);

        $candidate->hiring_status = $validated['hiring_status'];
        $candidate->save();

        return redirect()->route('talent-pool.index')->with('success', 'Candidate status updated successfully.');
    }

    public function approve(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:job_applications,id',
        ]);

        // Update application status to approved/interview
        $application = $candidate->applications()->findOrFail($validated['application_id']);
        $application->update([
            'status' => 'INTERVIEW',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Candidate approved for interview.');
    }

    public function reject(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:job_applications,id',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $application = $candidate->applications()->findOrFail($validated['application_id']);
        $application->update([
            'status' => 'REJECTED',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // TODO: Send notification to candidate

        return redirect()->back()->with('success', 'Candidate application rejected.');
    }

    public function revise(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:job_applications,id',
            'revision_notes' => 'required|string|max:1000',
        ]);

        $application = $candidate->applications()->findOrFail($validated['application_id']);
        $application->update([
            'status' => 'REVISION_REQUESTED',
            'revision_notes' => $validated['revision_notes'],
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // TODO: Send notification to candidate with revision notes

        return redirect()->back()->with('success', 'Revision request sent to candidate.');
    }

    public function getDocumentUrl(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'document_path' => 'required|string',
        ]);

        // Check if user has permission to view this candidate's documents
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('client')) {
            abort(403, 'Unauthorized access to candidate documents.');
        }

        // Generate temporary URL (valid for 5 minutes)
        $url = \Storage::disk('minio')->temporaryUrl(
            $validated['document_path'],
            now()->addMinutes(5)
        );

        return response()->json(['url' => $url]);
    }
}
