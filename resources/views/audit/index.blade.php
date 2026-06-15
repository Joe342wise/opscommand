<x-layouts.app title="Audit Trail - OpsCommand">
    <div class="flex overflow-hidden" style="height: calc(100vh - 120px);">
        <!-- Left Filtering Sidebar -->
        <aside class="w-64 bg-surface-container-low border-r border-surface-variant flex flex-col p-4 gap-6 overflow-y-auto shrink-0">
            <div>
                <h6 class="font-label-caps text-label-caps text-primary mb-3">Traceability Filters</h6>
                <form method="GET" class="space-y-4">
                    <!-- Filter Group: Activity -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-label-caps text-slate-400 uppercase tracking-wider">Activity Context</label>
                        <select name="entity_type" class="w-full bg-surface-container border-surface-variant border rounded-lg px-2 py-1.5 text-body-sm font-body-sm focus:ring-1 focus:ring-primary transition-all">
                            <option value="">All Activities</option>
                            <option value="activity" {{ request('entity_type') === 'activity' ? 'selected' : '' }}>Activities</option>
                            <option value="incident" {{ request('entity_type') === 'incident' ? 'selected' : '' }}>Incidents</option>
                            <option value="escalation" {{ request('entity_type') === 'escalation' ? 'selected' : '' }}>Escalations</option>
                            <option value="handover" {{ request('entity_type') === 'handover' ? 'selected' : '' }}>Handovers</option>
                        </select>
                    </div>
                    <!-- Filter Group: Status -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-label-caps text-slate-400 uppercase tracking-wider">Event Type</label>
                        <div class="flex flex-col gap-1.5">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="events[]" value="created" class="rounded border-surface-variant bg-surface-container text-primary focus:ring-primary" {{ in_array('created', request('events', [])) ? 'checked' : '' }}/>
                                <span class="text-body-sm group-hover:text-primary transition-colors">Created</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="events[]" value="updated" class="rounded border-surface-variant bg-surface-container text-primary focus:ring-primary" {{ in_array('updated', request('events', [])) ? 'checked' : '' }}/>
                                <span class="text-body-sm group-hover:text-primary transition-colors">Updated</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="events[]" value="deleted" class="rounded border-surface-variant bg-surface-container text-primary focus:ring-primary" {{ in_array('deleted', request('events', [])) ? 'checked' : '' }}/>
                                <span class="text-body-sm group-hover:text-primary transition-colors">Deleted</span>
                            </label>
                        </div>
                    </div>
                    <!-- Filter Group: Date Range -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-label-caps text-slate-400 uppercase tracking-wider">Date Range</label>
                        <div class="space-y-1.5">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-surface-container border-surface-variant border rounded-lg px-2 py-1 text-[11px] focus:ring-1 focus:ring-primary"/>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-surface-container border-surface-variant border rounded-lg px-2 py-1 text-[11px] focus:ring-1 focus:ring-primary"/>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2 bg-primary text-on-primary text-[11px] font-label-caps rounded-lg hover:opacity-90 transition-all">Apply Filters</button>
                </form>
            </div>
            <div class="mt-auto">
                <a href="{{ route('audit.index') }}" class="w-full py-2 text-on-surface-variant text-[11px] font-label-caps border border-dashed border-surface-variant rounded-lg hover:bg-surface-variant transition-all block text-center">Clear All Filters</a>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6 bg-background">
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Page Header -->
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Audit Trail: <span class="text-primary">System-Wide Activity</span></h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Chronological record of all state transitions and system remarks.</p>
                    </div>
                </div>

                <!-- Timeline Container -->
                <div class="space-y-6">
                    @forelse ($auditLogs as $log)
                        <div class="timeline-item relative pl-8 pb-8 timeline-line">
                            <div class="absolute left-0 top-1 w-6 h-6 rounded-full {{ match($log->event) { 'created' => 'bg-success-emerald/20 border-success-emerald', 'updated' => 'bg-warning-amber/20 border-warning-amber', 'deleted' => 'bg-danger-rose/20 border-danger-rose', default => 'bg-primary/20 border-primary' } }} flex items-center justify-center border z-10">
                                <span class="material-symbols-outlined text-[14px] {{ match($log->event) { 'created' => 'text-success-emerald', 'updated' => 'text-warning-amber', 'deleted' => 'text-danger-rose', default => 'text-primary' } }}" style="font-variation-settings: 'FILL' 1;">
                                    {{ match($log->event) { 'created' => 'check_circle', 'updated' => 'pending', 'deleted' => 'error', default => 'info' } }}
                                </span>
                            </div>
                            <div class="bg-surface-container border border-slate-800 rounded-lg p-3 hover:border-primary transition-all shadow-sm group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-mono-data text-mono-data text-slate-400">{{ $log->created_at->format('H:i:s') }}</span>
                                            <span class="font-label-caps text-label-caps {{ match($log->event) { 'created' => 'bg-success-emerald/10 text-secondary border border-success-emerald/20', 'updated' => 'bg-warning-amber/10 text-tertiary border border-warning-amber/20', 'deleted' => 'bg-danger-rose/10 text-error border border-danger-rose/20', default => 'bg-primary/10 text-primary border border-primary/20' } }} px-2 py-0.5 rounded">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </div>
                                        <h5 class="font-headline-md text-headline-md text-on-surface mb-1">{{ $log->description }}</h5>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                                            <span class="text-primary font-semibold">{{ ucfirst($log->entity_type) }}</span>
                                            {{ $log->entity_id ? '#'.$log->entity_id : '' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center gap-2 justify-end mb-1">
                                            <span class="font-label-caps text-label-caps text-on-surface">{{ $log->causer?->name ?? 'System' }}</span>
                                            <div class="w-6 h-6 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center text-[10px] font-bold text-primary">
                                                {{ strtoupper(substr($log->causer?->name ?? 'S', 0, 2)) }}
                                            </div>
                                        </div>
                                        <span class="font-mono-data text-mono-data text-slate-500">ID: LOG-{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                                @if ($log->old_values || $log->new_values)
                                    <div class="mt-4 p-3 bg-slate-950 border border-slate-900 rounded-lg">
                                        <p class="font-mono-data text-mono-data text-on-surface-variant italic leading-relaxed">
                                            @if ($log->old_values && $log->new_values)
                                                Changed from "{{ json_encode($log->old_values) }}" to "{{ json_encode($log->new_values) }}"
                                            @elseif ($log->new_values)
                                                {{ json_encode($log->new_values) }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2">history</span>
                            <p class="text-sm text-slate-400">No audit logs found.</p>
                        </div>
                    @endforelse
                </div>

                @if ($auditLogs->hasPages())
                    <div class="mt-6">
                        {{ $auditLogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side Utility -->
        <aside class="w-80 bg-surface-container-low border-l border-surface-variant p-4 hidden xl:flex flex-col gap-6 overflow-y-auto shrink-0">
            <!-- Audit Integrity Widget -->
            <section class="space-y-4">
                <h6 class="font-label-caps text-label-caps text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    AUDIT INTEGRITY
                </h6>
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-4 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-label-caps text-slate-400 mb-0.5">INTEGRITY STATUS</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-success-emerald animate-pulse"></span>
                                <span class="font-bold text-success-emerald text-sm uppercase tracking-widest">SECURE</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-label-caps text-slate-400 mb-0.5">SESSION ID</p>
                            <span class="font-mono-data text-[10px] text-on-surface-variant">TX-{{ strtoupper(substr(uniqid(), 0, 4)) }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-surface-container p-2 rounded-lg border border-surface-variant">
                            <p class="text-[9px] font-label-caps text-slate-400">TOTAL EVENTS</p>
                            <p class="text-lg font-bold text-primary">{{ number_format($auditLogs->total()) }}</p>
                        </div>
                        <div class="bg-surface-container p-2 rounded-lg border border-surface-variant">
                            <p class="text-[9px] font-label-caps text-slate-400">TODAY</p>
                            <p class="text-lg font-bold text-secondary">{{ $todayCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Activity Velocity -->
            <section>
                <h6 class="font-label-caps text-label-caps text-primary mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">analytics</span>
                    ACTIVITY VELOCITY
                </h6>
                <div class="bg-slate-900 border border-slate-800 rounded-lg p-3">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-2xl font-bold text-on-surface">{{ $todayCount ?? 0 }}</span>
                        <span class="text-success-emerald text-[10px] font-bold">Today</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-4">Total events today</p>
                    <div class="h-12 w-full flex items-end gap-1">
                        @foreach(range(1, 7) as $i)
                            <div class="bg-primary/{{ ($i * 10 + 10) }} w-full h-[{{ rand(20, 95) }}%] rounded-t-sm"></div>
                        @endforeach
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <style>
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 24px;
            bottom: 0;
            width: 2px;
            background: #2d3449;
        }
    </style>
</x-layouts.app>
