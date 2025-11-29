<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function updateStatus(Request $request, Application $application)
    {
        \Illuminate\Support\Facades\Log::info('Update Status Request Data:', $request->all());

        $validated = $request->validate([
            'status' => 'required|string|in:APPLIED,INTERVIEW,OFFERING,HIRED,PROCESSING_VISA,DEPLOYED,REJECTED',
            'interview_date' => 'nullable|date',
            'interview_location' => 'nullable|string',
            'interview_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];
        
        if ($request->has('interview_date')) $updateData['interview_date'] = $validated['interview_date'];
        if ($request->has('interview_location')) $updateData['interview_location'] = $validated['interview_location'];
        if ($request->has('interview_notes')) $updateData['interview_notes'] = $validated['interview_notes'];
        if ($request->has('admin_notes')) $updateData['admin_notes'] = $validated['admin_notes'];

        // Map status to pipeline step
        $statusToStep = [
            'APPLIED' => 1,
            'REVIEW' => 2,
            // 'INTERVIEW' => handled dynamically below
            'OFFERING' => 6,
            'HIRED' => 8,
            'PROCESSING_VISA' => 10,
            'DEPLOYED' => 12,
            'REJECTED' => 0, 
        ];

        if ($validated['status'] === 'INTERVIEW') {
            // Jika ada tanggal interview (baru diinput atau sudah ada di DB), masuk Step 4 (Interview)
            // Jika belum ada, masuk Step 3 (Interview Scheduling)
            $hasInterviewDate = !empty($validated['interview_date']) || !empty($application->interview_date);
            $updateData['current_step'] = $hasInterviewDate ? 4 : 3;
        } elseif (isset($statusToStep[$validated['status']]) && $validated['status'] !== 'REJECTED') {
            $updateData['current_step'] = $statusToStep[$validated['status']];
        }
        
        \Illuminate\Support\Facades\Log::info('Updating Application Status', [
            'id' => $application->id,
            'status' => $validated['status'],
            'mapped_step' => $updateData['current_step'] ?? 'not set',
            'update_data' => $updateData
        ]);

        $application->update($updateData);

        // Create readable status message
        $statusMessages = [
            'APPLIED' => 'Application Received',
            'INTERVIEW' => 'Interview Scheduled',
            'OFFERING' => 'Job Offered',
            'HIRED' => 'You are Hired!',
            'PROCESSING_VISA' => 'Visa Processing Started',
            'DEPLOYED' => 'Deployed',
            'REJECTED' => 'Application Rejected',
        ];

        $readableStatus = $statusMessages[$validated['status']] ?? ucwords(str_replace('_', ' ', strtolower($validated['status'])));
        $jobTitle = $application->job ? $application->job->title : 'Unknown Job';

        $message = "Your application for '{$jobTitle}' has been updated to: {$readableStatus}.";
        
        // Tambahkan detail pesan jika Interview
        if ($validated['status'] === 'INTERVIEW' && !empty($validated['interview_date'])) {
            $date = \Carbon\Carbon::parse($validated['interview_date'])->format('d M Y H:i');
            $message .= " Schedule: {$date}. Check details for more info.";
        }

        \App\Models\Notification::create([
            'user_id' => $application->candidate_id,
            'title' => 'Application Status Updated',
            'message' => $message,
            'type' => 'APPLICATION_STATUS_UPDATED',
            'related_id' => $application->id,
        ]);

        return back()->with('success', 'Application status updated successfully.');
    }
}
