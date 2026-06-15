<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Audit Logs</h1>
                <p class="text-sm text-surface-bright mt-1">Track all system changes and user actions</p>
            </div>
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Action</label>
                    <select name="action" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">Entity Type</label>
                    <select name="entity_type" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="">All</option>
                        <option value="App\Models\Activity" {{ request('entity_type') === 'App\Models\Activity' ? 'selected' : '' }}>Activity</option>
                        <option value="App\Models\Incident" {{ request('entity_type') === 'App\Models\Incident' ? 'selected' : '' }}>Incident</option>
                        <option value="App\Models\Escalation" {{ request('entity_type') === 'App\Models\Escalation' ? 'selected' : '' }}>Escalation</option>
                        <option value="App\Models\Handover" {{ request('entity_type') === 'App\Models\Handover' ? 'selected' : '' }}>Handover</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-bright mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                </div>
                <button type="submit" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                    Filter
                </button>
            </form>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Time</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Actor</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Action</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Entity</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">IP</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr class="border-b border-outline-variant hover:bg-surface-container-high transition-colors">
                            <td class="px-4 py-3 text-sm text-on-surface">{{ $log->created_at->format('M d, H:i:s') }}</td>
                            <td class="px-4 py-3 text-sm text-on-surface">{{ $log->actor?->name ?? 'System' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $log->action === 'created' ? 'bg-success-emerald/20 text-success-emerald' : ($log->action === 'updated' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-danger-rose/20 text-danger-rose') }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                            <td class="px-4 py-3 text-xs text-surface-bright font-mono">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('audit.show', $log) }}" class="text-xs text-primary hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-surface-bright">
                                <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">history</span>
                                <p>No audit logs found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="mt-4">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
