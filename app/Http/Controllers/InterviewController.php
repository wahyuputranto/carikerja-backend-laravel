<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function store(Request $request, Application $application)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date',
            'type' => 'required|in:ONLINE,OFFLINE',
            'meeting_link' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        // Create Interview
        Interview::create([
            'application_id' => $application->id,
            'interviewer_id' => Auth::id(),
            'scheduled_at' => $validated['scheduled_at'],
            'type' => $validated['type'],
            'meeting_link' => $validated['meeting_link'],
            'feedback_notes' => $validated['notes'],
        ]);

        // Update Application Status
        $application->update([
            'status' => 'INTERVIEW',
            'current_step' => 3, // Assuming step 3 is Interview
        ]);

        return back()->with('success', 'Interview scheduled successfully.');
    }
}
