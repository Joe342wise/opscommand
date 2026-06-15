<x-layouts.app title="Activities - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-on-surface">Activities</h1>
                <p class="text-sm text-outline mt-0.5">Manage operational activities</p>
            </div>
            <a href="{{ route('activities.create') }}"
               class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                New Activity
            </a>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-4 border-b border-outline-variant">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activities..."
                           class="flex-1 px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary">
                    <select name="status" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="priority" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Title</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Priority</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Owner</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Due Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('activities.show', $activity) }}" class="text-sm font-medium text-on-surface hover:text-primary">{{ $activity->title }}</a>
                                @if($activity->description)
                                    <p class="text-xs text-outline mt-0.5 truncate max-w-md">{{ $activity->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($activity->priority) {
                                        'critical' => 'bg-danger-rose/20 text-danger-rose',
                                        'high' => 'bg-warning-amber/20 text-warning-amber',
                                        'medium' => 'bg-primary-container/20 text-primary',
                                        'low' => 'bg-success-emerald/20 text-success-emerald',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ ucfirst($activity->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($activity->status) {
                                        'completed' => 'bg-success-emerald/20 text-success-emerald',
                                        'escalated' => 'bg-danger-rose/20 text-danger-rose',
                                        'in_progress' => 'bg-primary-container/20 text-primary',
                                        'pending' => 'bg-warning-amber/20 text-warning-amber',
                                        'cancelled' => 'bg-surface-container-high text-outline',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface">{{ $activity->owner->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3 text-sm text-outline">{{ $activity->due_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-outline">{{ $activity->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-outline">No activities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($activities->hasPages())
                <div class="p-4 border-t border-outline-variant">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
