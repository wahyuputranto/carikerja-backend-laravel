<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:APPLIED,INTERVIEW,OFFERING,PROCESSING_VISA,DEPLOYED,REJECTED',
        ]);

        $application->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Application status updated successfully.');
    }
}
