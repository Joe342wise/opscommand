<div wire:poll.15s="loadUnreadCount">
    <a href="{{ route('notifications.index') }}" class="relative material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors p-2 rounded-full hover:bg-surface-variant/30">
        notifications
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-danger-rose text-[10px] font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>
</div>
