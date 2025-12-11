<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];
        $recentJobs = [];
        $recentCandidates = [];

        if ($user->hasRole('superadmin') || $user->hasRole('recruiter')) {
            // Superadmin Stats
            $stats = [
                'total_jobs' => Job::count(),
                'active_jobs' => Job::where('status', 'PUBLISHED')->count(),
                'total_candidates' => Candidate::count(),
                'ready_candidates' => Candidate::where('hiring_status', 'READY_TO_HIRE')->count(),
                'total_users' => User::count(),
                'total_applications' => Application::count(),
            ];

            $recentJobs = Job::with(['client', 'jobCategory', 'jobLocation'])
                ->latest()
                ->take(5)
                ->get();

            $recentCandidates = Candidate::with('profile')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($candidate) {
                    if ($candidate->profile && $candidate->profile->photo_url) {
                        try {
                            // Use file proxy logic
                            $path = $candidate->profile->photo_url;
                            $candidate->profile->photo_url = route('talent-pool.file-proxy', [
                                'filename' => basename($path),
                                'path' => $path
                            ]);
                        } catch (\Exception $e) {
                            // Log error or keep original URL
                        }
                    }
                    return $candidate;
                });

        } elseif (auth()->guard('client')->check()) {
            // Client Stats
            $clientId = auth()->guard('client')->id();

            $stats = [
                'total_jobs' => Job::where('client_profile_id', $clientId)->count(),
                'active_jobs' => Job::where('client_profile_id', $clientId)
                    ->where('status', 'PUBLISHED')
                    ->count(),
                // Applications for this client's jobs
                'total_applications' => Application::whereHas('job', function ($query) use ($clientId) {
                    $query->where('client_profile_id', $clientId);
                })->count(),
            ];

            $recentJobs = Job::where('client_profile_id', $clientId)
                ->with(['jobCategory', 'jobLocation'])
                ->latest()
                ->take(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentJobs' => $recentJobs,
            'recentCandidates' => $recentCandidates,
        ]);
    }
}
