<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @group Notifications
 *
 * APIs for managing notifications
 */
class NotificationController extends Controller
{
    /**
     * List user notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    /**
     * Get unread notifications count and list
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();
        $notifications = $user->unreadNotifications()->orderBy('created_at', 'desc')->take(10)->get();
        return response()->json(['success' => true, 'data' => ['count' => $count, 'notifications' => $notifications]]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }
        $notification->markAsRead();
        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}
