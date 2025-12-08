<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function store(Request $request, Application $application = null)
    {
        $validated = $request->validate([
            'scheduled_at' => 'nullable|date',
            'type' => 'required|in:ONLINE,OFFLINE',
            'meeting_link' => 'required_if:type,ONLINE|nullable|url',
            'address' => 'required_if:type,OFFLINE|nullable|string',
            'notes' => 'nullable|string',
            'stage' => 'nullable|in:PRE_INTERVIEW,USER_INTERVIEW',
            'candidate_id' => 'nullable|exists:candidates,id',
        ]);

        $stage = $validated['stage'] ?? 'USER_INTERVIEW';

        // Create Interview
        if ($validated['scheduled_at']) {
            $interview = Interview::create([
                'application_id' => $application ? $application->id : null,
                'candidate_id' => $application ? $application->candidate_id : ($validated['candidate_id'] ?? null),
                'interviewer_id' => Auth::id(),
                'scheduled_at' => $validated['scheduled_at'],
                'type' => $validated['type'],
                'meeting_link' => $validated['meeting_link'],
                'location_address' => $validated['address'] ?? null,
                'feedback_notes' => $validated['notes'],
                'stage' => $stage,
            ]);

            Notification::create([
                'user_id' => $interview->candidate_id,
                'title' => 'Interview Scheduled',
                'message' => 'You have a new interview scheduled for ' . $interview->scheduled_at->format('d M Y H:i'),
                'type' => 'info',
                'is_read' => false,
                'related_id' => $interview->id,
            ]);

            // Notify Client if this is related to a job application
            if ($application && $application->job && $application->job->client_profile_id) {
                Notification::create([
                    'user_id' => $application->job->client_profile_id,
                    'title' => 'Interview Scheduled by Admin',
                    'message' => 'Admin has scheduled an interview for candidate ' . ($application->candidate ? $application->candidate->name : 'Unknown') . ' for job ' . $application->job->title . ' on ' . $interview->scheduled_at->format('d M Y H:i'),
                    'type' => 'interview_scheduled',
                    'is_read' => false,
                    'related_id' => $interview->id,
                ]);
            }
        }

        // If Application exists, update its status (User Interview flow)
        if ($application) {
            // Determine Step & Status
            $status = $application->status;
            $step = $application->current_step;

            if ($stage === 'USER_INTERVIEW') {
                $status = 'INTERVIEW';
                $step = !empty($validated['scheduled_at']) ? 4 : 3;
            }

            $application->update([
                'status' => $status,
                'current_step' => $step,
                'interview_date' => $validated['scheduled_at'],
                'interview_location' => $validated['type'] === 'ONLINE' ? $validated['meeting_link'] : 'Offline',
                'interview_address' => $validated['address'] ?? null,
                'interview_notes' => $validated['notes'],
            ]);
        }

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

    public function storePreInterviewResult(Request $request, Application $application = null)
    {
        $validated = $request->validate([
            'languages' => 'array',
            'languages.*.language' => 'required|string',
            'languages.*.speaking' => 'nullable|string',
            'languages.*.reading' => 'nullable|string',
            'languages.*.writing' => 'nullable|string',
            'computer_skills' => 'array',
            'computer_skills.*.skill_name' => 'required|string',
            'computer_skills.*.proficiency_level' => 'nullable|string',
            'notes' => 'nullable|string',
            'result' => 'required|in:PASSED,FAILED',
            'candidate_id' => 'required_without:application|exists:candidates,id',
        ]);

        $candidateId = $application ? $application->candidate_id : $validated['candidate_id'];
        $candidate = \App\Models\Candidate::findOrFail($candidateId);

        // 1. Update Candidate Languages
        $candidate->languages()->delete(); // Replace existing
        foreach ($validated['languages'] as $lang) {
            $candidate->languages()->create($lang);
        }

        // 2. Update Computer Skills
        $candidate->computerSkills()->delete(); // Replace existing
        if (!empty($validated['computer_skills'])) {
            foreach ($validated['computer_skills'] as $skill) {
                $candidate->computerSkills()->create($skill);
            }
        }

        // 3. Update Interview Record (Find the latest PRE_INTERVIEW for this candidate)
        $interview = Interview::where('candidate_id', $candidate->id)
            ->where('stage', 'PRE_INTERVIEW')
            ->latest()
            ->first();

        if ($interview) {
            $interview->update([
                'result' => $validated['result'],
                'feedback_notes' => $validated['notes'],
            ]);
        } else {
            // If no scheduled interview found, create a record of the result
            Interview::create([
                'application_id' => $application ? $application->id : null,
                'candidate_id' => $candidate->id,
                'interviewer_id' => Auth::id(),
                'stage' => 'PRE_INTERVIEW',
                'scheduled_at' => now(), // Assume done now
                'type' => 'OFFLINE', // Default
                'result' => $validated['result'],
                'feedback_notes' => $validated['notes'],
            ]);
        }

        // 4. Update Candidate Status if needed?
        // Maybe mark candidate as "Screened" or "Verified"?
        // For now, just saving the result is enough as per request.

        return back()->with('success', 'Pre-Interview result saved successfully.');
    }

    public function update(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date',
            'type' => 'required|in:ONLINE,OFFLINE',
            'meeting_link' => 'required_if:type,ONLINE|nullable|url',
            'address' => 'required_if:type,OFFLINE|nullable|string',
            'notes' => 'nullable|string',
        ]);

        $interview->update([
            'scheduled_at' => $validated['scheduled_at'],
            'type' => $validated['type'],
            'meeting_link' => $validated['meeting_link'],
            'location_address' => $validated['address'] ?? null,
            'feedback_notes' => $validated['notes'],
        ]);

        Notification::create([
            'user_id' => $interview->candidate_id,
            'title' => 'Interview Rescheduled',
            'message' => 'Your interview has been rescheduled to ' . $interview->scheduled_at->format('d M Y H:i'),
            'type' => 'info',
            'is_read' => false,
            'related_id' => $interview->id,
        ]);

        return back()->with('success', 'Interview rescheduled successfully.');
    }
}
