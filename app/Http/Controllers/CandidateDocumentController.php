<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

use App\Models\Notification;

class CandidateDocumentController extends Controller
{
    public function approve(Document $document)
    {
        $document->update([
            'status' => 'VALID',
            'rejection_note' => null,
        ]);

        Notification::create([
            'user_id' => $document->candidate_id,
            'title' => 'Document Approved',
            'message' => "Your document '{$document->documentType->name}' has been approved.",
            'type' => 'DOCUMENT_APPROVED',
            'related_id' => $document->id,
        ]);

        // Check if candidate is ready to hire
        $document->candidate->updateHiringStatusIfReady();

        return redirect()->back()->with('success', 'Document approved successfully.');
    }

    public function reject(Request $request, Document $document)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|max:255',
        ]);

        $document->update([
            'status' => 'INVALID',
            'rejection_note' => $validated['rejection_note'],
        ]);

        Notification::create([
            'user_id' => $document->candidate_id,
            'title' => 'Document Rejected',
            'message' => "Your document '{$document->documentType->name}' was rejected. Reason: {$validated['rejection_note']}",
            'type' => 'DOCUMENT_REJECTED',
            'related_id' => $document->id,
        ]);

        // Sync (downgrade if necessary) candidate status
        $document->candidate->syncHiringStatus();

        return redirect()->back()->with('success', 'Document rejected successfully.');
    }
}
