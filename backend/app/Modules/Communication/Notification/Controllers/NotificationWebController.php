<?php

namespace App\Modules\Communication\Notification\Controllers;

use App\Kernel\Http\Controllers\Controller;

class NotificationWebController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(25);
        return view('notifikasi.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return redirect()->back();
    }
}
