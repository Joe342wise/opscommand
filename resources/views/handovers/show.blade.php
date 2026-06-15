<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Handover #{{ $handover->id }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $handover->shift->name }} • Created by {{ $handover->createdBy?->name ?? 'Unknown' }} on {{ $handover->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($handover->status === 'draft')
                    <form method="POST" action="{{ route('handovers.update', $handover) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="px-4 py-2 bg-warning-amber text-background rounded-lg font-medium hover:opacity-90">Submit for Acknowledgement</button>
                    </form>
                @endif
                @if ($handover->status === 'pending')
                    <form method="POST" action="{{ route('handovers.acknowledge', $handover) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-success-emerald text-background rounded-lg font-medium hover:opacity-90">Acknowledge</button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-3">Summary</h2>
                <p class="text-sm text-surface-bright whitespace-pre-wrap">{{ $handover->summary }}</p>
            </div>

            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-3">Status</h2>
                <span class="px-3 py-1 text-sm rounded-full {{ $handover->status === 'completed' ? 'bg-success-emerald/20 text-success-emerald' : ($handover->status === 'acknowledged' ? 'bg-primary/20 text-primary' : ($handover->status === 'pending' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-surface-container-high text-surface-bright')) }}">
                    {{ ucfirst($handover->status) }}
                </span>
                @if ($handover->risk_summary)
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-on-surface mb-1">Risk Summary</h3>
                        <p class="text-sm text-surface-bright whitespace-pre-wrap">{{ $handover->risk_summary }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <h2 class="text-lg font-medium text-on-surface mb-4">Handover Items</h2>
            @if ($handover->items->count() > 0)
                <div class="space-y-2">
                    @foreach ($handover->items as $item)
                        <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full {{ $item->item_type === 'incident' ? 'bg-danger-rose/20 text-danger-rose' : ($item->item_type === 'escalation' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-primary/20 text-primary') }} flex items-center justify-center text-sm">
                                    <span class="material-symbols-outlined text-lg">{{ $item->item_type === 'incident' ? 'warning' : ($item->item_type === 'escalation' ? 'trending_up' : 'task_alt') }}</span>
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ $item->description }}</p>
                                    <p class="text-xs text-surface-bright">{{ ucfirst($item->item_type) }} • {{ ucfirst($item->priority) }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $item->priority === 'critical' ? 'bg-danger-rose/20 text-danger-rose' : ($item->priority === 'high' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-surface-container-high text-surface-bright') }}">
                                {{ ucfirst($item->priority) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-surface-bright">No items attached to this handover.</p>
            @endif
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <h2 class="text-lg font-medium text-on-surface mb-4">Acknowledgements</h2>
            @if ($handover->acknowledgements->count() > 0)
                <div class="space-y-2">
                    @foreach ($handover->acknowledgements as $ack)
                        <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-success-emerald/20 text-success-emerald flex items-center justify-center text-sm">
                                    <span class="material-symbols-outlined text-lg">check</span>
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ $ack->acknowledgedBy?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-surface-bright">{{ $ack->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-success-emerald/20 text-success-emerald">Acknowledged</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-surface-bright">No acknowledgements yet.</p>
            @endif
        </div>
    </div>
</x-layouts.app>
