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

        $this->checkAndUpdateCandidateStatus($document->candidate_id);

        return redirect()->back()->with('success', 'Document approved successfully.');
    }

    private function checkAndUpdateCandidateStatus(string $candidateId)
    {
        $candidate = \App\Models\Candidate::find($candidateId);
        if (!$candidate) return;

        // 1. Check if all MANDATORY documents are VALID
        $mandatoryTypes = \App\Models\DocumentType::where('is_mandatory', true)->pluck('id');
        $validMandatoryDocs = \App\Models\Document::where('candidate_id', $candidateId)
            ->whereIn('document_type_id', $mandatoryTypes)
            ->where('status', 'VALID')
            ->count();
        
        $allDocumentsApproved = ($validMandatoryDocs >= $mandatoryTypes->count());

        // 2. Check if Pre-Interview is PASSED
        // We look for 'PRE_INTERVIEW' stage and 'PASSED' result in interviews
        $preInterviewPassed = $candidate->interviews()
            ->where('stage', 'PRE_INTERVIEW')
            ->where('result', 'PASSED')
            ->exists();

        // 3. Update Status
        if ($allDocumentsApproved && $preInterviewPassed) {
            $candidate->update(['hiring_status' => 'READY_TO_HIRE']);
        }
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

        return redirect()->back()->with('success', 'Document rejected successfully.');
    }
}
