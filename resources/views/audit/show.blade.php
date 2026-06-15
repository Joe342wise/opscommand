<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Audit Log #{{ $auditLog->id }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ ucfirst($auditLog->action) }} {{ class_basename($auditLog->entity_type) }} #{{ $auditLog->entity_id }}</p>
            </div>
            <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">Back to Logs</a>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-4">Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">Timestamp</span>
                        <span class="text-sm text-on-surface">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">Actor</span>
                        <span class="text-sm text-on-surface">{{ $auditLog->actor?->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">Action</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $auditLog->action === 'created' ? 'bg-success-emerald/20 text-success-emerald' : ($auditLog->action === 'updated' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-danger-rose/20 text-danger-rose') }}">
                            {{ ucfirst($auditLog->action) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">Entity Type</span>
                        <span class="text-sm text-on-surface">{{ class_basename($auditLog->entity_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">Entity ID</span>
                        <span class="text-sm text-on-surface">{{ $auditLog->entity_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">IP Address</span>
                        <span class="text-sm text-on-surface font-mono">{{ $auditLog->ip_address ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-surface-bright">User Agent</span>
                        <span class="text-sm text-on-surface text-right max-w-[300px] truncate">{{ $auditLog->user_agent ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @if ($auditLog->old_values)
                    <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                        <h2 class="text-lg font-medium text-on-surface mb-4">Old Values</h2>
                        <pre class="text-xs text-surface-bright font-mono bg-surface-container-low rounded-lg p-3 overflow-auto max-h-64">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                @if ($auditLog->new_values)
                    <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                        <h2 class="text-lg font-medium text-on-surface mb-4">New Values</h2>
                        <pre class="text-xs text-surface-bright font-mono bg-surface-container-low rounded-lg p-3 overflow-auto max-h-64">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
