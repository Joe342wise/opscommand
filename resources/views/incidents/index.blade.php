<x-layouts.app title="Incidents - OpsCommand">
    <!-- Page Header & Controls -->
    <div class="flex flex-col gap-4">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-on-surface-variant font-mono-data text-mono-data">PROJECTS / OPS-2024 /</span>
                    <span class="text-primary font-mono-data text-mono-data">INCIDENTS</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Incident Management</h2>
            </div>
            <div class="flex flex-col items-end gap-3">
                <a href="{{ route('incidents.create') }}" class="flex items-center gap-2 bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold hover:opacity-90 transition-all active:scale-95 shadow-lg shadow-indigo-500/10">
                    <span class="material-symbols-outlined fill-icon">add</span>
                    <span class="font-body-sm text-body-sm">New Incident</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex items-center justify-between py-2 border-b border-surface-variant/30">
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-high border border-surface-variant text-on-surface-variant cursor-pointer hover:border-outline transition-all">
                        <span class="material-symbols-outlined text-[14px]">filter_list</span>
                        <select name="status" class="bg-transparent border-none text-[11px] font-bold uppercase tracking-wider text-on-surface-variant focus:ring-0 p-0">
                            <option value="">Status: All</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-high border border-surface-variant text-on-surface-variant cursor-pointer hover:border-outline transition-all">
                        <span class="material-symbols-outlined text-[14px]">priority_high</span>
                        <select name="severity" class="bg-transparent border-none text-[11px] font-bold uppercase tracking-wider text-on-surface-variant focus:ring-0 p-0">
                            <option value="">Severity: All</option>
                            <option value="P1" {{ request('severity') === 'P1' ? 'selected' : '' }}>P1 Critical</option>
                            <option value="P2" {{ request('severity') === 'P2' ? 'selected' : '' }}>P2 High</option>
                            <option value="P3" {{ request('severity') === 'P3' ? 'selected' : '' }}>P3 Medium</option>
                            <option value="P4" {{ request('severity') === 'P4' ? 'selected' : '' }}>P4 Low</option>
                        </select>
                    </div>
                    <button type="submit" class="text-on-surface-variant hover:text-primary transition-colors font-label-caps text-label-caps">Apply</button>
                    @if (request()->hasAny(['status', 'severity']))
                        <a href="{{ route('incidents.index') }}" class="text-on-surface-variant hover:text-danger-rose transition-colors font-label-caps text-label-caps">Clear Filters</a>
                    @endif
                </form>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-mono-data text-on-surface-variant italic">{{ $incidents->total() }} incidents</span>
            </div>
        </div>
    </div>

    <!-- High-Density Data Table -->
    <div class="bg-surface-container-low rounded-xl border border-surface-variant overflow-hidden flex flex-col shadow-2xl">
        <div class="overflow-x-auto custom-scrollbar flex-1">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-container-high text-on-surface-variant uppercase tracking-wider text-left sticky top-0 z-10">
                        <th class="w-12 px-4 py-3"><input class="rounded bg-slate-900 border-slate-700 text-primary-container focus:ring-primary-container" type="checkbox"/></th>
                        <th class="px-4 py-3 font-label-caps text-label-caps min-w-[300px]">Incident</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Severity</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Status</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Assignee</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Service</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Created</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/20">
                    @forelse ($incidents as $incident)
                        <tr class="hover:bg-slate-900/50 transition-colors group cursor-pointer" onclick="window.location='{{ route('incidents.show', $incident) }}'">
                            <td class="px-4 py-2.5" onclick="event.stopPropagation()"><input class="rounded bg-slate-900 border-slate-700 text-primary-container focus:ring-primary-container" type="checkbox"/></td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-{{ match($incident->severity) { 'P1' => 'danger-rose', 'P2' => 'warning-amber', 'P3' => 'primary', 'P4' => 'success-emerald', default => 'slate-400' } }}">
                                        {{ $incident->status === 'resolved' ? 'check_circle' : ($incident->status === 'investigating' ? 'search' : 'error_outline') }}
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="text-body-sm font-semibold text-slate-100">{{ $incident->title }}</span>
                                        <span class="text-mono-data text-slate-400">INC-{{ str_pad($incident->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ match($incident->severity) {
                                        'P1' => 'bg-danger-rose/10 text-danger-rose border border-danger-rose/20',
                                        'P2' => 'bg-warning-amber/10 text-warning-amber border border-warning-amber/20',
                                        'P3' => 'bg-primary/10 text-primary border border-primary/20',
                                        'P4' => 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20',
                                        default => 'bg-surface-container-high text-outline border border-surface-variant',
                                    } }} uppercase">
                                    {{ $incident->severity }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ match($incident->status) {
                                        'resolved' => 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20',
                                        'investigating' => 'bg-primary/10 text-primary border border-primary/20',
                                        'open' => 'bg-danger-rose/10 text-danger-rose border border-danger-rose/20',
                                        'closed' => 'bg-surface-container-high text-outline border border-surface-variant',
                                        default => 'bg-surface-container-high text-outline border border-surface-variant',
                                    } }} uppercase">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($incident->owner->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="text-body-sm text-slate-300">{{ $incident->owner->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-slate-300">{{ $incident->service?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 font-mono-data text-slate-400">{{ $incident->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <button class="row-hover-action p-1 rounded hover:bg-slate-800 text-on-surface-variant">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline mb-2">warning</span>
                                <p class="text-sm text-slate-400">No incidents found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($incidents->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-high">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #020617; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 2px; }
        .row-hover-action { opacity: 0; transition: opacity 0.15s ease-in-out; }
        tr:hover .row-hover-action { opacity: 1; }
    </style>
</x-layouts.app>
