<x-layouts.app title="Dashboard - OpsCommand">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-on-surface">Operations Dashboard</h1>
                <p class="text-sm text-outline mt-0.5">Real-time operational overview</p>
            </div>
            <div class="text-xs text-outline font-mono" x-data="{ date: '{{ now()->format('D, M d Y') }}' }" x-text="date"></div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <a href="{{ route('incidents.index') }}" class="bg-surface-container p-4 rounded-xl border border-outline-variant hover:border-primary/50 transition-colors">
                <p class="text-xs text-outline uppercase tracking-wide">Active Incidents</p>
                <p class="text-2xl font-semibold text-danger-rose mt-1">{{ $stats['active_incidents'] }}</p>
                <p class="text-xs text-outline mt-1">{{ $stats['total_incidents'] }} total</p>
            </a>
            <a href="{{ route('activities.index') }}" class="bg-surface-container p-4 rounded-xl border border-outline-variant hover:border-primary/50 transition-colors">
                <p class="text-xs text-outline uppercase tracking-wide">Pending Activities</p>
                <p class="text-2xl font-semibold text-warning-amber mt-1">{{ $stats['pending_activities'] }}</p>
                <p class="text-xs text-outline mt-1">{{ $stats['completed_activities'] }} completed</p>
            </a>
            <a href="{{ route('escalations.index') }}" class="bg-surface-container p-4 rounded-xl border border-outline-variant hover:border-primary/50 transition-colors">
                <p class="text-xs text-outline uppercase tracking-wide">Open Escalations</p>
                <p class="text-2xl font-semibold text-primary mt-1">{{ $stats['open_escalations'] }}</p>
                <p class="text-xs text-outline mt-1">{{ $stats['total_escalations'] }} total</p>
            </a>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Active Personnel</p>
                <p class="text-2xl font-semibold text-success-emerald mt-1">{{ $stats['total_personnel'] }}</p>
                <p class="text-xs text-outline mt-1">Across all teams</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <h2 class="text-sm font-medium text-on-surface mb-3">Activities by Priority</h2>
                <div class="space-y-2">
                    @foreach (['critical' => 'text-danger-rose', 'high' => 'text-warning-amber', 'medium' => 'text-primary', 'low' => 'text-success-emerald'] as $priority => $color)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-outline">{{ ucfirst($priority) }}</span>
                            <span class="text-sm font-medium {{ $color }}">{{ $activitiesByPriority[$priority] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <h2 class="text-sm font-medium text-on-surface mb-3">Incidents by Severity</h2>
                <div class="space-y-2">
                    @foreach (['P1' => 'text-danger-rose', 'P2' => 'text-warning-amber', 'P3' => 'text-primary', 'P4' => 'text-outline'] as $severity => $color)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-outline">{{ $severity }}</span>
                            <span class="text-sm font-medium {{ $color }}">{{ $incidentsBySeverity[$severity] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <h2 class="text-sm font-medium text-on-surface mb-3">Escalations by Status</h2>
                <div class="space-y-2">
                    @foreach (['pending' => 'text-warning-amber', 'in_progress' => 'text-primary', 'resolved' => 'text-success-emerald', 'closed' => 'text-outline'] as $status => $color)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-outline">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                            <span class="text-sm font-medium {{ $color }}">{{ $escalationsByStatus[$status] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-medium text-on-surface">Recent Activities</h2>
                    <a href="{{ route('activities.index') }}" class="text-xs text-primary hover:underline">View All</a>
                </div>
                @forelse ($recentActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-container-high transition-colors {{ !$loop->last ? 'border-b border-outline-variant' : '' }}">
                        <span class="w-6 h-6 rounded-full {{ $activity->status === 'completed' ? 'bg-success-emerald/20 text-success-emerald' : ($activity->status === 'in_progress' ? 'bg-primary/20 text-primary' : 'bg-surface-container-high text-surface-bright') }} flex items-center justify-center text-xs">
                            <span class="material-symbols-outlined text-sm">{{ $activity->status === 'completed' ? 'check' : ($activity->status === 'in_progress' ? 'pending' : 'schedule') }}</span>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-on-surface truncate">{{ $activity->title }}</p>
                            <p class="text-xs text-outline">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $activity->priority === 'critical' ? 'bg-danger-rose/20 text-danger-rose' : ($activity->priority === 'high' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-surface-container-high text-surface-bright') }}">
                            {{ ucfirst($activity->priority) }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-outline">No recent activities.</p>
                @endforelse
            </div>

            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-medium text-on-surface">Recent Incidents</h2>
                    <a href="{{ route('incidents.index') }}" class="text-xs text-primary hover:underline">View All</a>
                </div>
                @forelse ($recentIncidents as $incident)
                    <a href="{{ route('incidents.show', $incident) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-container-high transition-colors {{ !$loop->last ? 'border-b border-outline-variant' : '' }}">
                        <span class="w-6 h-6 rounded-full {{ $incident->status === 'resolved' ? 'bg-success-emerald/20 text-success-emerald' : 'bg-danger-rose/20 text-danger-rose' }} flex items-center justify-center text-xs">
                            <span class="material-symbols-outlined text-sm">{{ $incident->status === 'resolved' ? 'check' : 'warning' }}</span>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-on-surface truncate">{{ $incident->title }}</p>
                            <p class="text-xs text-outline">{{ $incident->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $incident->severity === 'P1' ? 'bg-danger-rose/20 text-danger-rose' : ($incident->severity === 'P2' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-surface-container-high text-surface-bright') }}">
                            {{ $incident->severity }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-outline">No recent incidents.</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <a href="{{ route('handovers.index') }}" class="bg-surface-container rounded-xl border border-outline-variant p-5 hover:border-primary/50 transition-colors">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">swap_horiz</span>
                    </span>
                    <h2 class="text-sm font-medium text-on-surface">Handover Board</h2>
                </div>
                <p class="text-2xl font-semibold text-on-surface">{{ $stats['pending_handovers'] }}</p>
                <p class="text-xs text-outline mt-1">Pending handovers</p>
            </a>

            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-lg bg-success-emerald/20 text-success-emerald flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">task_alt</span>
                    </span>
                    <h2 class="text-sm font-medium text-on-surface">Activity Progress</h2>
                </div>
                <div class="w-full bg-surface-container-high rounded-full h-2 mt-2">
                    @php $completionRate = $stats['total_activities'] > 0 ? ($stats['completed_activities'] / $stats['total_activities']) * 100 : 0; @endphp
                    <div class="bg-success-emerald h-2 rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
                </div>
                <p class="text-xs text-outline mt-2">{{ number_format($completionRate, 1) }}% completion rate</p>
            </div>

            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-8 h-8 rounded-lg bg-warning-amber/20 text-warning-amber flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">trending_up</span>
                    </span>
                    <h2 class="text-sm font-medium text-on-surface">Escalation Rate</h2>
                </div>
                @php $escalationRate = $stats['total_activities'] > 0 ? ($stats['open_escalations'] / $stats['total_activities']) * 100 : 0; @endphp
                <p class="text-2xl font-semibold text-on-surface">{{ number_format($escalationRate, 1) }}%</p>
                <p class="text-xs text-outline mt-1">{{ $stats['open_escalations'] }} of {{ $stats['total_activities'] }} activities</p>
            </div>
        </div>
    </div>
</x-layouts.app>
