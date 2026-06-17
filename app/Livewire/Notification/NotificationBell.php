<?php

namespace App\Livewire\Notification;

use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount(): void
    {
        if (Auth::check()) {
            $this->unreadCount = NotificationRecipient::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        }
    }

    public function render()
    {
        return view('livewire.notification.notification-bell');
    }
}
