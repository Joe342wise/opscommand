<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Notifications</h1>
                <p class="text-sm text-surface-bright mt-1">Your notifications and alerts</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('notifications.alerts') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                    View Alerts
                </a>
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg hover:opacity-90">
                        Mark All as Read
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-2">
            @forelse ($notifications as $recipient)
                <div class="bg-surface-container rounded-xl p-4 border border-outline-variant {{ !$recipient->is_read ? 'border-l-2 border-l-primary' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full {{ $recipient->notification->category === 'critical' ? 'bg-danger-rose/20 text-danger-rose' : ($recipient->notification->category === 'warning' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-primary/20 text-primary') }} flex items-center justify-center text-sm mt-0.5">
                                <span class="material-symbols-outlined text-lg">{{ $recipient->notification->category === 'critical' ? 'error' : ($recipient->notification->category === 'warning' ? 'warning' : 'info') }}</span>
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-medium text-on-surface">{{ $recipient->notification->title }}</h3>
                                    @if (!$recipient->is_read)
                                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-surface-bright mt-1">{{ $recipient->notification->message }}</p>
                                <p class="text-xs text-outline mt-2">{{ $recipient->notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if (!$recipient->is_read)
                            <form method="POST" action="{{ route('notifications.mark-read', $recipient) }}">
                                @csrf
                                <button type="submit" class="text-xs text-primary hover:underline">Mark Read</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-surface-container rounded-xl p-12 border border-outline-variant text-center">
                    <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">notifications_off</span>
                    <p class="text-surface-bright">No notifications.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
