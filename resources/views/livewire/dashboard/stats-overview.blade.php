<div wire:poll.30s="refreshStats" class="space-y-6">
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-surface-container border-l-4 border-l-primary p-card-padding border border-surface-variant rounded">
            <div class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Active Incidents</div>
            <div class="font-mono-data text-2xl text-on-surface mt-1">{{ $stats['active_incidents'] ?? 0 }}</div>
        </div>
        <div class="bg-surface-container border-l-4 border-l-warning-amber p-card-padding border border-surface-variant rounded">
            <div class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Escalated</div>
            <div class="font-mono-data text-2xl text-on-surface mt-1">{{ $stats['open_escalations'] ?? 0 }}</div>
        </div>
        <div class="bg-surface-container border-l-4 border-l-danger-rose p-card-padding border border-surface-variant rounded">
            <div class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Overdue</div>
            <div class="font-mono-data text-2xl text-on-surface mt-1">{{ $stats['overdue_activities'] ?? 0 }}</div>
        </div>
        <div class="bg-surface-container border-l-4 border-l-secondary p-card-padding border border-surface-variant rounded">
            <div class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Pending Handover</div>
            <div class="font-mono-data text-2xl text-on-surface mt-1">{{ $stats['pending_handovers'] ?? 0 }}</div>
        </div>
        <div class="bg-surface-container border-l-4 border-l-success-emerald p-card-padding border border-surface-variant rounded">
            <div class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Team On-Deck</div>
            <div class="font-mono-data text-2xl text-on-surface mt-1">{{ $stats['active_personnel'] ?? 0 }}</div>
        </div>
    </div>
</div>
