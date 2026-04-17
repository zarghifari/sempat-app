<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read
        
        $query = Auth::user()->notifications();
        
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Auth::user()->unreadNotifications()->count();
        
        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }
}
