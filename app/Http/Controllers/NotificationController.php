<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = NotificationRecipient::with('notification.alert')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(NotificationRecipient $recipient)
    {
        if ($recipient->user_id === auth()->id()) {
            $recipient->update(['is_read' => true, 'read_at' => now()]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        NotificationRecipient::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function alerts(Request $request)
    {
        $query = Alert::with('createdBy');

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $alerts = $query->latest()->paginate(20);

        return view('notifications.alerts', compact('alerts'));
    }

    public function acknowledgeAlert(Alert $alert)
    {
        $alert->update(['status' => 'acknowledged']);

        return redirect()->back()->with('success', 'Alert acknowledged.');
    }

    public function resolveAlert(Alert $alert)
    {
        $alert->update(['status' => 'resolved']);

        return redirect()->back()->with('success', 'Alert resolved.');
    }

    public static function getUnreadCount(): int
    {
        return NotificationRecipient::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }
}
