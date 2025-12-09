<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\AuditTrail::with('user')
            ->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('module', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('reference_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20)->withQueryString();

        return \Inertia\Inertia::render('AuditTrail/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'module', 'action']),
        ]);
    }
}
