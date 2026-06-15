<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Alerts</h1>
                <p class="text-sm text-surface-bright mt-1">System alerts and warnings</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                Back to Notifications
            </a>
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Severity</label>
                    <select name="severity" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Status</label>
                    <select name="status" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                    Filter
                </button>
            </form>
        </div>

        <div class="space-y-2">
            @forelse ($alerts as $alert)
                <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full {{ $alert->severity === 'critical' ? 'bg-danger-rose/20 text-danger-rose' : ($alert->severity === 'warning' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-primary/20 text-primary') }} flex items-center justify-center text-sm mt-0.5">
                                <span class="material-symbols-outlined text-lg">{{ $alert->severity === 'critical' ? 'error' : ($alert->severity === 'warning' ? 'warning' : 'info') }}</span>
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-medium text-on-surface">{{ $alert->title }}</h3>
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $alert->status === 'active' ? 'bg-danger-rose/20 text-danger-rose' : ($alert->status === 'acknowledged' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-success-emerald/20 text-success-emerald') }}">
                                        {{ ucfirst($alert->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-surface-bright mt-1">{{ $alert->message }}</p>
                                <p class="text-xs text-outline mt-2">By {{ $alert->createdBy?->name ?? 'System' }} • {{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if ($alert->status === 'active')
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('notifications.alerts.acknowledge', $alert) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs bg-warning-amber/20 text-warning-amber rounded-lg hover:opacity-90">Acknowledge</button>
                                </form>
                                <form method="POST" action="{{ route('notifications.alerts.resolve', $alert) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs bg-success-emerald/20 text-success-emerald rounded-lg hover:opacity-90">Resolve</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-surface-container rounded-xl p-12 border border-outline-variant text-center">
                    <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">check_circle</span>
                    <p class="text-surface-bright">No alerts found.</p>
                </div>
            @endforelse
        </div>

        @if ($alerts->hasPages())
            <div class="mt-4">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
