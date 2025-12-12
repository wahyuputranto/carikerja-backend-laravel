<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Deployment;
use Illuminate\Http\Request;

class DeploymentController extends Controller
{
    public function store(Request $request, Application $application, $stage)
    {
        // Validate based on stage
        $rules = [];
        if ($stage === 'CONTRACT') {
            $rules = [
                'contract_number' => 'required|string',
                'signed_at' => 'required|date',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'offering_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Add file validation
            ];
        } elseif ($stage === 'MEDICAL') {
            $rules = [
                'medical_status' => 'required|in:FIT,UNFIT,PENDING',
                'check_date' => 'required|date',
            ];
        } elseif ($stage === 'VISA') {
            $rules = [
                'visa_number' => 'required|string',
                'visa_expiry' => 'required|date',
            ];
        } elseif ($stage === 'FLIGHT') {
            $rules = [
                'flight_airline' => 'required|string',
                'flight_number' => 'required|string',
                'departure_at' => 'required|date',
                'arrival_at' => 'required|date',
            ];
        }

        $validated = $request->validate($rules);

        // Find or create deployment record
        $deployment = Deployment::firstOrCreate(
            ['application_id' => $application->id]
        );

        // Handle File Upload for CONTRACT stage
        if ($stage === 'CONTRACT' && $request->hasFile('offering_letter')) {
            $path = $request->file('offering_letter')->store('offering-letters', 'minio'); // Use 'minio' disk
            $application->update(['offering_letter_path' => $path]);
        }

        // Remove file from validated array before updating deployment model (since it's not in deployment table)
        unset($validated['offering_letter']);

        $deployment->update($validated);

        // Update Application Status & Step based on stage
        $status = $application->status;
        $step = $application->current_step;

        if ($stage === 'CONTRACT') {
            $status = 'OFFERING'; 
            $step = 5; 
            // Update Candidate Status to HIRED
            $application->candidate()->update(['hiring_status' => 'HIRED']);
        } elseif ($stage === 'MEDICAL') {
            $status = 'PROCESSING_VISA';
            $step = 7;
        } elseif ($stage === 'VISA') {
            $status = 'PROCESSING_VISA';
            $step = 9;
        } elseif ($stage === 'FLIGHT') {
            $status = 'DEPLOYED';
            $step = 11;
        }

        $application->update([
            'status' => $status,
            'current_step' => $step,
        ]);

        return back()->with('success', "Deployment stage ($stage) updated successfully.");
    }
}
