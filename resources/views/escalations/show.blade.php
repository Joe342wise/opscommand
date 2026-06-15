<x-layouts.app title="Escalation - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-outline mb-2">
                <a href="{{ route('escalations.index') }}" class="hover:text-primary">Escalations</a>
                <span>/</span>
                <span class="text-on-surface">Escalation #{{ $escalation->id }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-on-surface">Escalation #{{ $escalation->id }}</h1>
                    <p class="text-sm text-outline mt-0.5">Created {{ $escalation->created_at->diffForHumans() }} by {{ $escalation->createdBy->name ?? 'Unknown' }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                        {{ match($escalation->priority) {
                            'critical' => 'bg-danger-rose/20 text-danger-rose',
                            'high' => 'bg-warning-amber/20 text-warning-amber',
                            'medium' => 'bg-primary-container/20 text-primary',
                            'low' => 'bg-success-emerald/20 text-success-emerald',
                            default => 'bg-surface-container-high text-outline',
                        } }}">
                        {{ ucfirst($escalation->priority) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                        {{ match($escalation->status) {
                            'resolved' => 'bg-success-emerald/20 text-success-emerald',
                            'in_progress' => 'bg-primary-container/20 text-primary',
                            'pending' => 'bg-warning-amber/20 text-warning-amber',
                            'cancelled' => 'bg-surface-container-high text-outline',
                            default => 'bg-surface-container-high text-outline',
                        } }}">
                        {{ ucfirst(str_replace('_', ' ', $escalation->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-3">Details</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-outline">Target Team</span>
                            <p class="text-on-surface mt-0.5">{{ $escalation->targetTeam->name ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Owner</span>
                            <p class="text-on-surface mt-0.5">{{ $escalation->owner->name ?? '—' }}</p>
                        </div>
                        @if ($escalation->activity)
                            <div>
                                <span class="text-outline">Linked Activity</span>
                                <p class="text-on-surface mt-0.5">
                                    <a href="{{ route('activities.show', $escalation->activity) }}" class="text-primary hover:underline">{{ $escalation->activity->title }}</a>
                                </p>
                            </div>
                        @endif
                        @if ($escalation->incident)
                            <div>
                                <span class="text-outline">Linked Incident</span>
                                <p class="text-on-surface mt-0.5">
                                    <a href="{{ route('incidents.show', $escalation->incident) }}" class="text-warning-amber hover:underline">{{ $escalation->incident->title }}</a>
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-outline-variant">
                        <span class="text-outline text-sm">Reason</span>
                        <p class="text-on-surface text-sm mt-1">{{ $escalation->reason }}</p>
                    </div>
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Status History</h2>
                    @forelse ($escalation->histories as $history)
                        <div class="flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-outline-variant' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs text-outline shrink-0">
                                {{ substr($history->changedBy->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-on-surface">{{ $history->changedBy->name ?? 'Unknown' }}</span>
                                    @if ($history->previous_status && $history->new_status)
                                        <span class="text-xs text-outline">changed status from</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $history->previous_status)) }}</span>
                                        <span class="text-xs text-outline">to</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</span>
                                    @endif
                                </div>
                                @if ($history->summary)
                                    <p class="text-sm text-outline mt-1">{{ $history->summary }}</p>
                                @endif
                                <p class="text-xs text-outline mt-1">{{ $history->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-outline">No status changes yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Update Status</h2>
                    <form method="POST" action="{{ route('escalations.update', $escalation) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <select name="status" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="pending" {{ $escalation->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $escalation->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $escalation->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="cancelled" {{ $escalation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <textarea name="update_summary" rows="2" placeholder="Add a note about this change..."
                                  class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
