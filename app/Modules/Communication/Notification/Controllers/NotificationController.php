<?php

namespace App\Modules\Communication\Notification\Controllers;

use App\Kernel\Http\Controllers\Controller;
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
        return $this->paginated($notifications);
    }

    /**
     * Get unread notifications count and list
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();
        $notifications = $user->unreadNotifications()->orderBy('created_at', 'desc')->take(10)->get();
        return $this->success(['count' => $count, 'notifications' => $notifications]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return $this->notFound('Notification not found');
        }
        $notification->markAsRead();
        return $this->success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return $this->success(null, 'All notifications marked as read');
    }
}
