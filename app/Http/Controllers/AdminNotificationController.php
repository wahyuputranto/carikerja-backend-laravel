<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminNotificationController extends Controller
{
    public function inbox(Request $request)
    {
        $status = $request->query('status', 'all'); // all, unread, read
        
        $query = AdminNotification::with(['candidate', 'client'])
            ->orderBy('created_at', 'desc');

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(10)->withQueryString();

        return Inertia::render('AdminNotifications/Index', [
            'notifications' => $notifications,
            'filters' => [
                'status' => $status
            ]
        ]);
    }
    public function index()
    {
        $notifications = AdminNotification::with('candidate')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = AdminNotification::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    // API endpoint untuk Go backend untuk membuat notifikasi
    public function store(Request $request)
    {
        // Validate API key for internal service-to-service communication
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('app.internal_api_key');
        
        if (!$apiKey || $apiKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'candidate_id' => 'required|uuid|exists:candidates,id',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'url' => 'nullable|string'
        ]);

        $notification = AdminNotification::create($validated);

        return response()->json([
            'success' => true,
            'data' => $notification
        ], 201);
    }
}
