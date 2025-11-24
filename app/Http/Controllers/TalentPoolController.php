<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TalentPoolController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['profile', 'educations', 'experiences'])
            ->where('hiring_status', 'READY_TO_HIRE');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        // Filter by skills (if implemented)
        if ($request->filled('skills')) {
            // This would require a skills relationship
            // $query->whereHas('skills', function ($q) use ($request) {
            //     $q->whereIn('skill_id', $request->skills);
            // });
        }

        $candidates = $query->latest()->paginate(15);

        return Inertia::render('TalentPool/Index', [
            'candidates' => $candidates,
            'filters' => $request->only(['search', 'skills']),
        ]);
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'profile',
            'educations',
            'experiences',
            'applications.job',
        ]);

        return Inertia::render('TalentPool/Show', [
            'candidate' => $candidate,
        ]);
    }

    public function updateStatus(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'hiring_status' => 'required|in:AVAILABLE,READY_TO_HIRE,HIRED,NOT_AVAILABLE',
        ]);

        $candidate->update($validated);

        return redirect()->back()->with('success', 'Candidate status updated successfully.');
    }
}
