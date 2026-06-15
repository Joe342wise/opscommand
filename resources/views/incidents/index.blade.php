<x-layouts.app title="Incidents - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-on-surface">Incidents</h1>
                <p class="text-sm text-outline mt-0.5">Track and manage operational incidents</p>
            </div>
            <a href="{{ route('incidents.create') }}"
               class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                Report Incident
            </a>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-4 border-b border-outline-variant">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search incidents..."
                           class="flex-1 px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary">
                    <select name="status" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <select name="severity" class="px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">All Severity</option>
                        <option value="P1" {{ request('severity') === 'P1' ? 'selected' : '' }}>P1 Critical</option>
                        <option value="P2" {{ request('severity') === 'P2' ? 'selected' : '' }}>P2 High</option>
                        <option value="P3" {{ request('severity') === 'P3' ? 'selected' : '' }}>P3 Medium</option>
                        <option value="P4" {{ request('severity') === 'P4' ? 'selected' : '' }}>P4 Low</option>
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
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Severity</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Owner</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Service</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-outline uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidents as $incident)
                        <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('incidents.show', $incident) }}" class="text-sm font-medium text-on-surface hover:text-primary">{{ $incident->title }}</a>
                                @if($incident->description)
                                    <p class="text-xs text-outline mt-0.5 truncate max-w-md">{{ $incident->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($incident->severity) {
                                        'P1' => 'bg-danger-rose/20 text-danger-rose',
                                        'P2' => 'bg-warning-amber/20 text-warning-amber',
                                        'P3' => 'bg-primary-container/20 text-primary',
                                        'P4' => 'bg-success-emerald/20 text-success-emerald',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ $incident->severity }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($incident->status) {
                                        'resolved' => 'bg-success-emerald/20 text-success-emerald',
                                        'investigating' => 'bg-warning-amber/20 text-warning-amber',
                                        'in_progress' => 'bg-primary-container/20 text-primary',
                                        'open' => 'bg-danger-rose/20 text-danger-rose',
                                        'closed' => 'bg-surface-container-high text-outline',
                                        default => 'bg-surface-container-high text-outline',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface">{{ $incident->owner->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3 text-sm text-outline">{{ $incident->service->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-outline">{{ $incident->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-outline">No incidents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($incidents->hasPages())
                <div class="p-4 border-t border-outline-variant">
                    {{ $incidents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
