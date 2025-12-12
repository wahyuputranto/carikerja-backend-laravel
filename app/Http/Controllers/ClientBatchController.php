<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientBatch;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Validation\Rule;

class ClientBatchController extends Controller
{
    public function index(Client $client)
    {
        return Inertia::render('ClientBatches/Index', [
            'client' => $client,
            'batches' => $client->batches()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'batch_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('client_batches')->where(function ($query) use ($client) {
                    return $query->where('client_id', $client->id);
                }),
            ],
            'total_quota' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Default is_active to true if not present (matching DB default)
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;
        
        // Strict Validation: Ensure only 1 active batch
        if ($isActive) {
            $hasActiveBatch = $client->batches()->where('is_active', true)->exists();
            if ($hasActiveBatch) {
                 return redirect()->back()->with('error', 'Hanya boleh ada satu batch yang aktif. Silakan nonaktifkan batch yang sedang berjalan terlebih dahulu.');
            }
        }
        
        // Explicitly set is_active in validated data
        $validated['is_active'] = $isActive;

        $client->batches()->create($validated);

        return redirect()->back()->with('success', 'Batch created successfully.');
    }

    public function update(Request $request, Client $client, ClientBatch $batch)
    {
        $validated = $request->validate([
            'batch_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('client_batches')->where(function ($query) use ($client) {
                    return $query->where('client_id', $client->id);
                })->ignore($batch->id),
            ],
            'total_quota' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Only check if we are explicitly setting it to active
        if ($request->has('is_active') && $request->boolean('is_active')) {
             // Check if ANY OTHER active batch exists
             $hasOtherActive = $client->batches()
                 ->where('id', '!=', $batch->id)
                 ->where('is_active', true)
                 ->exists();
                 
             if ($hasOtherActive) {
                 return redirect()->back()->with('error', 'Hanya boleh ada satu batch yang aktif. Silakan nonaktifkan batch lain terlebih dahulu.');
             }
        }

        $batch->update($validated);

        return redirect()->back()->with('success', 'Batch updated successfully.');
    }

    public function destroy(Client $client, ClientBatch $batch)
    {
        if ($batch->used_quota > 0) {
            return redirect()->back()->with('error', 'Cannot delete batch that has used quota.');
        }

        $batch->delete();
        return redirect()->back()->with('success', 'Batch deleted successfully.');
    }
}
