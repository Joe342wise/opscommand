<x-layouts.app title="{{ $activity->title }} - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-outline mb-2">
                <a href="{{ route('activities.index') }}" class="hover:text-primary">Activities</a>
                <span>/</span>
                <span class="text-on-surface">{{ $activity->title }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-on-surface">{{ $activity->title }}</h1>
                    <p class="text-sm text-outline mt-0.5">Created {{ $activity->created_at->diffForHumans() }} by {{ $activity->createdBy->name ?? 'Unknown' }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                        {{ match($activity->priority) {
                            'critical' => 'bg-danger-rose/20 text-danger-rose',
                            'high' => 'bg-warning-amber/20 text-warning-amber',
                            'medium' => 'bg-primary-container/20 text-primary',
                            'low' => 'bg-success-emerald/20 text-success-emerald',
                            default => 'bg-surface-container-high text-outline',
                        } }}">
                        {{ ucfirst($activity->priority) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
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
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-3">Details</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-outline">Owner</span>
                            <p class="text-on-surface mt-0.5">{{ $activity->owner->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Category</span>
                            <p class="text-on-surface mt-0.5">{{ $activity->category ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Due Date</span>
                            <p class="text-on-surface mt-0.5">{{ $activity->due_at?->format('M d, Y H:i') ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Last Updated</span>
                            <p class="text-on-surface mt-0.5">{{ $activity->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if ($activity->description)
                        <div class="mt-4 pt-4 border-t border-outline-variant">
                            <span class="text-outline text-sm">Description</span>
                            <p class="text-on-surface text-sm mt-1">{{ $activity->description }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Status Updates</h2>
                    @forelse ($activity->updates as $update)
                        <div class="flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-outline-variant' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs text-outline shrink-0">
                                {{ substr($update->updatedBy->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-on-surface">{{ $update->updatedBy->name ?? 'Unknown' }}</span>
                                    @if ($update->previous_status && $update->new_status)
                                        <span class="text-xs text-outline">changed status from</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $update->previous_status)) }}</span>
                                        <span class="text-xs text-outline">to</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $update->new_status)) }}</span>
                                    @endif
                                </div>
                                @if ($update->summary)
                                    <p class="text-sm text-outline mt-1">{{ $update->summary }}</p>
                                @endif
                                <p class="text-xs text-outline mt-1">{{ $update->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-outline">No status updates yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Update Status</h2>
                    <form method="POST" action="{{ route('activities.update', $activity) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <select name="status" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="pending" {{ $activity->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $activity->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="escalated" {{ $activity->status === 'escalated' ? 'selected' : '' }}>Escalated</option>
                            <option value="completed" {{ $activity->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $activity->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <textarea name="update_summary" rows="2" placeholder="Add a note about this change..."
                                  class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                            Update Status
                        </button>
                    </form>
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Remarks</h2>
                    <form method="POST" action="{{ route('activities.remark', $activity) }}" class="space-y-3">
                        @csrf
                        <textarea name="remark" rows="3" required placeholder="Add a remark..."
                                  class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors">
                            Add Remark
                        </button>
                    </form>

                    <div class="mt-4 space-y-3">
                        @forelse ($activity->remarks as $remark)
                            <div class="p-3 bg-surface-container-low rounded-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-on-surface">{{ $remark->createdBy->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-outline">{{ $remark->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-outline">{{ $remark->remark }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-outline">No remarks yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
