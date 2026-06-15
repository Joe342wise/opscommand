<x-layouts.app title="Escalations - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-on-surface">Escalations</h1>
                <p class="text-sm text-outline mt-0.5">Manage escalated activities and incidents</p>
            </div>
            <a href="{{ route('escalations.create') }}"
               class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                New Escalation
            </a>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-4 border-b border-outline-variant">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search escalations..."
                           class="flex-1 px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary">
                    <select name="status" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="target_team_id" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ request('target_team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Reason</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Priority</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Target Team</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Source</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($escalations as $escalation)
                        <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('escalations.show', $escalation) }}" class="text-sm font-medium text-on-surface hover:text-primary">{{ Str::limit($escalation->reason, 60) }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($escalation->priority) {
                                        'critical' => 'bg-danger-rose/20 text-danger-rose',
                                        'high' => 'bg-warning-amber/20 text-warning-amber',
                                        'medium' => 'bg-primary-container/20 text-primary',
                                        'low' => 'bg-success-emerald/20 text-success-emerald',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ ucfirst($escalation->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($escalation->status) {
                                        'resolved' => 'bg-success-emerald/20 text-success-emerald',
                                        'in_progress' => 'bg-primary-container/20 text-primary',
                                        'pending' => 'bg-warning-amber/20 text-warning-amber',
                                        'cancelled' => 'bg-surface-container-high text-outline',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $escalation->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface">{{ $escalation->targetTeam->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-outline">
                                @if ($escalation->activity)
                                    <span class="text-primary">Activity: {{ Str::limit($escalation->activity->title, 30) }}</span>
                                @elseif ($escalation->incident)
                                    <span class="text-warning-amber">Incident: {{ Str::limit($escalation->incident->title, 30) }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-outline">{{ $escalation->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-outline">No escalations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($escalations->hasPages())
                <div class="p-4 border-t border-outline-variant">
                    {{ $escalations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
