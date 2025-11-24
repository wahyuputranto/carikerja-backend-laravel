<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterDocumentType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = MasterDocumentType::all();
        return Inertia::render('Admin/DocumentTypes/Index', [
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_mandatory' => 'boolean',
            'chunkable' => 'boolean',
            'allowed_mimetypes' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        MasterDocumentType::create($validated);

        return redirect()->back()->with('success', 'Document Type created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterDocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_mandatory' => 'boolean',
            'chunkable' => 'boolean',
            'allowed_mimetypes' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $documentType->update($validated);

        return redirect()->back()->with('success', 'Document Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterDocumentType $documentType)
    {
        $documentType->delete();
        return redirect()->back()->with('success', 'Document Type deleted successfully.');
    }
}
