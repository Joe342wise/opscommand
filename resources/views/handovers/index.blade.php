<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Handover Board</h1>
                <p class="text-sm text-surface-bright mt-1">Manage shift handovers and acknowledgements</p>
            </div>
            <a href="{{ route('handovers.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">
                New Handover
            </a>
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Status</label>
                    <select name="status" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Shift</label>
                    <select name="shift_id" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                    Filter
                </button>
            </form>
        </div>

        <div class="space-y-3">
            @forelse ($handovers as $handover)
                <a href="{{ route('handovers.show', $handover) }}" class="block bg-surface-container rounded-xl p-4 border border-outline-variant hover:border-primary/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center text-sm">
                                <span class="material-symbols-outlined text-lg">swap_horiz</span>
                            </span>
                            <div>
                                <h3 class="font-medium text-on-surface">Handover #{{ $handover->id }}</h3>
                                <p class="text-sm text-surface-bright">{{ $handover->shift->name }} • {{ $handover->createdBy?->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $handover->status === 'completed' ? 'bg-success-emerald/20 text-success-emerald' : ($handover->status === 'acknowledged' ? 'bg-primary/20 text-primary' : ($handover->status === 'pending' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-surface-container-high text-surface-bright')) }}">
                                {{ ucfirst($handover->status) }}
                            </span>
                            <span class="text-sm text-surface-bright">{{ $handover->created_at->format('M d, H:i') }}</span>
                            <span class="material-symbols-outlined text-surface-bright">chevron_right</span>
                        </div>
                    </div>
                    @if ($handover->summary)
                        <p class="mt-2 text-sm text-surface-bright line-clamp-2">{{ Str::limit($handover->summary, 150) }}</p>
                    @endif
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-surface-bright">{{ $handover->items()->count() }} items</span>
                        <span class="text-surface-bright">•</span>
                        <span class="text-xs text-surface-bright">{{ $handover->acknowledgements()->where('status', 'acknowledged')->count() }} acknowledged</span>
                    </div>
                </a>
            @empty
                <div class="bg-surface-container rounded-xl p-12 border border-outline-variant text-center">
                    <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">swap_horiz</span>
                    <p class="text-surface-bright">No handovers found.</p>
                </div>
            @endforelse
        </div>

        @if ($handovers->hasPages())
            <div class="mt-4">
                {{ $handovers->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
