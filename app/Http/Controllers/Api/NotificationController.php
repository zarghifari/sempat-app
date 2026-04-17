<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $filter = $request->input('filter', 'all'); // all, unread, read
        
        $query = $request->user()->notifications();
        
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        // Get all notifications and sort by priority + sort_timestamp
        $notifications = $query->get()->sortByDesc(function($notification) {
            // Sort by sort_timestamp DESC (newest first), then by priority DESC (parent last)
            $data = $notification->data;
            $sortTimestamp = $data['sort_timestamp'] ?? $notification->created_at->timestamp;
            $priority = $data['priority'] ?? 0;
            
            // Return combined sort key: higher timestamp = newer, higher priority = later
            // We want: newest first, but same timestamp → higher priority last
            return $sortTimestamp * 1000 - $priority; // Subtract priority so higher priority appears later
        })->values();
        
        // Paginate manually
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $notifications->slice($offset, $perPage)->values();
        $total = $notifications->count();
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => (int)ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get recent notifications (for dropdown).
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        
        // Get notifications and sort by priority + sort_timestamp
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit($limit * 2) // Get more to ensure we have enough after sorting
            ->get()
            ->sortByDesc(function($notification) {
                $data = $notification->data;
                $sortTimestamp = $data['sort_timestamp'] ?? $notification->created_at->timestamp;
                $priority = $data['priority'] ?? 0;
                return $sortTimestamp * 1000 - $priority;
            })
            ->take($limit)
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
