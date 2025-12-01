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

            $recentJobs = Job::with(['clientProfile.user', 'jobCategory', 'location'])
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

        } elseif ($user->hasRole('client')) {
            // Client Stats
            $clientProfileId = $user->clientProfile->id;

            $stats = [
                'total_jobs' => Job::where('client_profile_id', $clientProfileId)->count(),
                'active_jobs' => Job::where('client_profile_id', $clientProfileId)
                    ->where('status', 'PUBLISHED')
                    ->count(),
                // Applications for this client's jobs
                'total_applications' => Application::whereHas('job', function ($query) use ($clientProfileId) {
                    $query->where('client_profile_id', $clientProfileId);
                })->count(),
            ];

            $recentJobs = Job::where('client_profile_id', $clientProfileId)
                ->with(['jobCategory', 'location'])
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
