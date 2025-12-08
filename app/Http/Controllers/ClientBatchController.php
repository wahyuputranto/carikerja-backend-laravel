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

        // If setting as active, deactivate others
        if ($request->is_active) {
            $client->batches()->update(['is_active' => false]);
        }

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

        if ($request->is_active) {
            $client->batches()->where('id', '!=', $batch->id)->update(['is_active' => false]);
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
