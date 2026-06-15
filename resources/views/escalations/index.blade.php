<x-layouts.app title="Escalations - OpsCommand">
    <!-- Page Header & Controls -->
    <div class="flex flex-col gap-4">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-on-surface-variant font-mono-data text-mono-data">PROJECTS / OPS-2024 /</span>
                    <span class="text-primary font-mono-data text-mono-data">ESCALATIONS</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Escalation Management</h2>
            </div>
            <div class="flex flex-col items-end gap-3">
                <a href="{{ route('escalations.create') }}" class="flex items-center gap-2 bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold hover:opacity-90 transition-all active:scale-95 shadow-lg shadow-indigo-500/10">
                    <span class="material-symbols-outlined fill-icon">add</span>
                    <span class="font-body-sm text-body-sm">New Escalation</span>
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
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-high border border-surface-variant text-on-surface-variant cursor-pointer hover:border-outline transition-all">
                        <span class="material-symbols-outlined text-[14px]">priority_high</span>
                        <select name="priority" class="bg-transparent border-none text-[11px] font-bold uppercase tracking-wider text-on-surface-variant focus:ring-0 p-0">
                            <option value="">Priority: All</option>
                            <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <button type="submit" class="text-on-surface-variant hover:text-primary transition-colors font-label-caps text-label-caps">Apply</button>
                    @if (request()->hasAny(['status', 'priority']))
                        <a href="{{ route('escalations.index') }}" class="text-on-surface-variant hover:text-danger-rose transition-colors font-label-caps text-label-caps">Clear Filters</a>
                    @endif
                </form>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-mono-data text-on-surface-variant italic">{{ $escalations->total() }} escalations</span>
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
                        <th class="px-4 py-3 font-label-caps text-label-caps min-w-[300px]">Escalation</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Priority</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Status</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Target Team</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Assignee</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps">Created</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/20">
                    @forelse ($escalations as $escalation)
                        <tr class="hover:bg-slate-900/50 transition-colors group cursor-pointer" onclick="window.location='{{ route('escalations.show', $escalation) }}'">
                            <td class="px-4 py-2.5" onclick="event.stopPropagation()"><input class="rounded bg-slate-900 border-slate-700 text-primary-container focus:ring-primary-container" type="checkbox"/></td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-{{ match($escalation->priority) { 'critical' => 'danger-rose', 'high' => 'warning-amber', 'medium' => 'primary', 'low' => 'success-emerald', default => 'slate-400' } }}">
                                        {{ $escalation->status === 'resolved' ? 'check_circle' : 'priority_high' }}
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="text-body-sm font-semibold text-slate-100">{{ $escalation->title }}</span>
                                        <span class="text-mono-data text-slate-400">ESC-{{ str_pad($escalation->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ match($escalation->priority) {
                                        'critical' => 'bg-danger-rose/10 text-danger-rose border border-danger-rose/20',
                                        'high' => 'bg-warning-amber/10 text-warning-amber border border-warning-amber/20',
                                        'medium' => 'bg-primary/10 text-primary border border-primary/20',
                                        'low' => 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20',
                                        default => 'bg-surface-container-high text-outline border border-surface-variant',
                                    } }} uppercase">
                                    {{ ucfirst($escalation->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ match($escalation->status) {
                                        'resolved' => 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20',
                                        'in_progress' => 'bg-primary/10 text-primary border border-primary/20',
                                        'pending' => 'bg-warning-amber/10 text-warning-amber border border-warning-amber/20',
                                        default => 'bg-surface-container-high text-outline border border-surface-variant',
                                    } }} uppercase">
                                    {{ ucfirst(str_replace('_', ' ', $escalation->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-slate-300">{{ $escalation->targetTeam?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($escalation->owner->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="text-body-sm text-slate-300">{{ $escalation->owner->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 font-mono-data text-slate-400">{{ $escalation->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <button class="row-hover-action p-1 rounded hover:bg-slate-800 text-on-surface-variant">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline mb-2">trending_up</span>
                                <p class="text-sm text-slate-400">No escalations found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($escalations->hasPages())
            <div class="p-4 border-t border-surface-variant bg-surface-container-high">
                {{ $escalations->links() }}
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
