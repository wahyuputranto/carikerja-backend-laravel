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
        ]);

        $validated['slug'] = Str::slug($validated['name']);

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
        ]);

        if ($request->name !== $documentType->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $documentType->update($validated);

        return redirect()->back()->with('success', 'Document Type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        try {
            $documentType->delete();
            return redirect()->back()->with('success', 'Document Type deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23503') {
                return redirect()->back()->with('error', 'Cannot delete this Document Type because it is currently assigned to one or more candidates.');
            }
            throw $e;
        }
    }
}
