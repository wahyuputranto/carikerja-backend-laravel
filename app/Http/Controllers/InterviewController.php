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
            'scheduled_at' => 'nullable|date',
            'type' => 'required|in:ONLINE,OFFLINE',
            'meeting_link' => 'required_if:type,ONLINE|nullable|url',
            'address' => 'required_if:type,OFFLINE|nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Create Interview (Keep existing logic for history/record)
        if ($validated['scheduled_at']) {
            Interview::create([
                'application_id' => $application->id,
                'interviewer_id' => Auth::id(),
                'scheduled_at' => $validated['scheduled_at'],
                'type' => $validated['type'],
                'meeting_link' => $validated['meeting_link'],
                'location_address' => $validated['address'] ?? null, // Assuming Interview model has this or we ignore it there
                'feedback_notes' => $validated['notes'],
            ]);
        }

        // Determine Step
        $step = 3; // Default Scheduling
        if (!empty($validated['scheduled_at'])) {
            $step = 4; // Interview Scheduled
        }

        // Update Application Status & Sync Details
        $application->update([
            'status' => 'INTERVIEW',
            'current_step' => $step,
            'interview_date' => $validated['scheduled_at'],
            'interview_location' => $validated['type'] === 'ONLINE' ? $validated['meeting_link'] : 'Offline', // For frontend display compatibility
            'interview_address' => $validated['address'] ?? null,
            'interview_notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Interview scheduled successfully.');
    }

    public function storeFeedback(Request $request, Application $application)
    {
        $validated = $request->validate([
            'feedback' => 'required|string',
            'status' => 'nullable|in:INTERVIEW,OFFERING,REJECTED',
        ]);

        $updateData = [
            'interview_feedback' => $validated['feedback'],
        ];

        // Handle Status Change
        if (!empty($validated['status']) && $validated['status'] !== $application->status) {
            $updateData['status'] = $validated['status'];
            
            // Map status to pipeline step (Simplified logic from ApplicationController)
            $statusToStep = [
                'INTERVIEW' => 4, // Already scheduled
                'OFFERING' => 6,
                'REJECTED' => 0, 
            ];

            if (isset($statusToStep[$validated['status']])) {
                $updateData['current_step'] = $statusToStep[$validated['status']];
            }
        }

        $application->update($updateData);

        return back()->with('success', 'Interview feedback saved successfully.');
    }
}
