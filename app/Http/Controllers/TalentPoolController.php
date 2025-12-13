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
        $query = Candidate::with(['profile', 'educations', 'experiences', 'applications.job', 'documents.documentType']);

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

        $totalMandatoryDocs = \App\Models\DocumentType::where('is_mandatory', true)->count();

        // Generate temporary URLs for profile photos and calculate progress
        $candidates->getCollection()->transform(function ($candidate) use ($totalMandatoryDocs) {
            if ($candidate->profile && $candidate->profile->photo_url) {
                try {
                    // Use file proxy instead of direct temporaryUrl to avoid Mixed Content/CORS
                    $path = $candidate->profile->photo_url;
                    $candidate->profile->photo_url = route('talent-pool.file-proxy', [
                        'filename' => basename($path),
                        'path' => $path
                    ]);
                } catch (\Exception $e) {
                    // Log error or keep original URL
                }
            }
            
            // Calculate document progress
            $uploadedMandatory = $candidate->documents->filter(function ($doc) {
                return $doc->documentType && $doc->documentType->is_mandatory;
            })->count();

            $candidate->document_progress = $totalMandatoryDocs > 0 
                ? round(($uploadedMandatory / $totalMandatoryDocs) * 100) 
                : 0;

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
            'profile.interestedJobCategory',
            'personalDetail',
            'passports',
            'emergencyContacts',
            'nonFormalEducations',
            'languages',
            'computerSkills',
            'interviews', // Load candidate level interviews
            'preInterviews', // Load candidate level pre-interviews
            'educations',
            'experiences',
            'documents.documentType',
            'applications.job.client',
            'applications.job.jobLocation',
            'applications.deployment', // Load deployment details
            'applications.documents', // Load client uploaded documents
            'cv', // Load candidate CV
        ]);

        if ($candidate->profile && $candidate->profile->photo_url) {
            try {
                // Use file proxy
                $path = $candidate->profile->photo_url;
                $candidate->profile->photo_url = route('talent-pool.file-proxy', [
                    'filename' => basename($path),
                    'path' => $path
                ]);
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
    $user = auth()->user();
    $authorized = false;

    if ($user instanceof \App\Models\Client) {
        $authorized = true;
    } elseif ($user instanceof \App\Models\User) {
        // Allow if superadmin or has candidate.view permission
        if ($user->hasRole('superadmin') || $user->hasPermission('candidate.view')) {
            $authorized = true;
        }
        // Fallback for old check style, though unlikely needed if hasPermission is used
        elseif ($user->hasRole('client')) {
             $authorized = true;
        }
    }

    if (!$authorized) {
        abort(403, 'Unauthorized access to candidate documents.');
    }

        // Return proxy URL for documents too
        $path = $validated['document_path'];
        $url = route('talent-pool.file-proxy', [
            'filename' => basename($path),
            'path' => $path
        ]);

        return response()->json(['url' => $url]);
    }

    public function generateCV(Request $request, \App\Services\RabbitMQService $rabbitMQ, Candidate $candidate)
    {
        // Publish to RabbitMQ
        $rabbitMQ->publish('generate.cv.pdf', [
            'candidate_id' => $candidate->id,
            'requested_by' => auth()->id(),
            'template' => 'standard'
        ]);

        return back()->with('success', 'CV generation request sent to background worker.');
    }

    public function fileProxy(Request $request, $filename = null)
    {
        $validated = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $validated['path'];

        // Security check: ensure user is authenticated
        if (!auth()->check()) {
            abort(403);
        }

        // Check if file exists in MinIO
        if (!\Storage::disk('minio')->exists($path)) {
            abort(404);
        }

        // Stream the file content
        $stream = \Storage::disk('minio')->readStream($path);
        $mimeType = \Storage::disk('minio')->mimeType($path);
        
        // Use provided filename or fallback to basename
        $displayFilename = $filename ?? basename($path);
        // Sanitize for header
        $asciiFilename = \Illuminate\Support\Str::ascii($displayFilename);

        // Usage of standard storage response helper which adds Content-Length automatically
        return \Storage::disk('minio')->response($path, $asciiFilename, [
            'Content-Disposition' => 'inline; filename="' . $asciiFilename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function approveCv(Request $request, \App\Models\CandidateCv $cv)
    {
        $cv->update([
            'status' => 'VALID',
            'rejection_note' => null,
        ]);

        // TODO: Notification and Status Update logic similar to CandidateDocumentController
        // Since CV is mandatory, we should nominally check if candidate can be moved to READY_TO_HIRE
        $cv->candidate->updateHiringStatusIfReady();
        
        return redirect()->back()->with('success', 'CV approved successfully.');
    }

    public function rejectCv(Request $request, \App\Models\CandidateCv $cv)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|max:255',
        ]);

        $cv->update([
            'status' => 'INVALID',
            'rejection_note' => $validated['rejection_note'],
        ]);

        // TODO: Notification

        return redirect()->back()->with('success', 'CV rejected successfully.');
    }
}
