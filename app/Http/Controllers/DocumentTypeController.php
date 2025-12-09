<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentType::latest()->get();
        return Inertia::render('MasterData/DocumentTypes/Index', [
            'documentTypes' => $documentTypes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_mandatory' => 'boolean',
            'chunkable' => 'boolean',
            'allowed_mimetypes' => 'nullable|array',
            'allowed_mimetypes.*' => 'in:' . implode(',', DocumentType::ALLOWED_MIMETYPES),
            'max_size' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'template' => 'nullable|file|max:10240', // 10MB max for template
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('template')) {
            $path = $request->file('template')->store('document_templates', 'minio');
            $validated['template'] = \Illuminate\Support\Facades\Storage::disk('minio')->url($path);
        }

        DocumentType::create($validated);

        return redirect()->back()->with('success', 'Document Type created successfully.');
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_mandatory' => 'boolean',
            'chunkable' => 'boolean',
            'allowed_mimetypes' => 'nullable|array',
            'allowed_mimetypes.*' => 'in:' . implode(',', DocumentType::ALLOWED_MIMETYPES),
            'max_size' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'template' => 'nullable|file|max:10240',
        ]);

        if ($request->name !== $documentType->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('template')) {
            // Delete old template if exists
            if ($documentType->template) {
                // Try to delete from public first (legacy check)
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($documentType->template)) {
                     \Illuminate\Support\Facades\Storage::disk('public')->delete($documentType->template);
                }
            }
            $path = $request->file('template')->store('document_templates', 'minio');
            $validated['template'] = \Illuminate\Support\Facades\Storage::disk('minio')->url($path);
        }

        $documentType->update($validated);

        return redirect()->back()->with('success', 'Document Type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();
        return redirect()->back()->with('success', 'Document Type deleted successfully.');
    }
}
