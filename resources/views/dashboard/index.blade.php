<x-layouts.app title="Dashboard - OpsCommand">
    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-surface-container-high border-l-4 border-l-danger-rose py-3 px-5 border border-surface-variant rounded flex flex-col justify-between">
            <span class="font-label-caps text-label-caps text-danger-rose uppercase font-bold tracking-widest">Active Incidents</span>
            <div class="mt-2 flex items-center justify-between">
                <span class="font-headline-lg text-[28px] text-on-surface font-black">{{ sprintf('%02d', $stats['active_incidents']) }}</span>
                <span class="material-symbols-outlined text-danger-rose bg-danger-rose/10 p-1.5 rounded">emergency</span>
            </div>
        </div>
        <div class="bg-surface-container-high border-l-4 border-l-warning-amber py-3 px-5 border border-surface-variant rounded flex flex-col justify-between">
            <span class="font-label-caps text-label-caps text-warning-amber uppercase font-bold tracking-widest">Escalated</span>
            <div class="mt-2 flex items-center justify-between">
                <span class="font-headline-lg text-[28px] text-on-surface font-black">{{ sprintf('%02d', $stats['open_escalations']) }}</span>
                <span class="material-symbols-outlined text-warning-amber bg-warning-amber/10 p-1.5 rounded">trending_up</span>
            </div>
        </div>
        <div class="bg-surface-container-high border-l-4 border-l-error py-3 px-5 border border-surface-variant rounded flex flex-col justify-between">
            <span class="font-label-caps text-label-caps text-error uppercase font-bold tracking-widest">Overdue</span>
            <div class="mt-2 flex items-center justify-between">
                <span class="font-headline-lg text-[28px] text-on-surface font-black">{{ sprintf('%02d', $stats['overdue_activities']) }}</span>
                <span class="material-symbols-outlined text-error bg-error/10 p-1.5 rounded">alarm_off</span>
            </div>
        </div>
        <div class="bg-surface-container-high border-l-4 border-l-primary py-3 px-5 border border-surface-variant rounded flex flex-col justify-between">
            <span class="font-label-caps text-label-caps text-primary uppercase font-bold tracking-widest">Pending Handover</span>
            <div class="mt-2 flex items-center justify-between">
                <span class="font-headline-lg text-[28px] text-on-surface font-black">{{ sprintf('%02d', $stats['pending_handovers']) }}</span>
                <span class="material-symbols-outlined text-primary bg-primary/10 p-1.5 rounded">output</span>
            </div>
        </div>
        <div class="bg-surface-container-high border-l-4 border-l-success-emerald py-3 px-5 border border-surface-variant rounded flex flex-col justify-between">
            <span class="font-label-caps text-label-caps text-success-emerald uppercase font-bold tracking-widest">Team On-Deck</span>
            <div class="mt-2 flex items-center justify-between">
                <div class="flex -space-x-2">
                    @foreach($stats['online_personnel']->take(3) as $person)
                        <div class="w-8 h-8 rounded-full bg-primary-container border-2 border-surface-container-high flex items-center justify-center text-on-primary-container text-xs font-bold">
                            {{ strtoupper(substr($person->name, 0, 1)) }}
                        </div>
                    @endforeach
                    @if($stats['total_personnel'] > 3)
                        <div class="w-8 h-8 rounded-full bg-surface-variant border-2 border-surface-container-high flex items-center justify-center text-on-surface-variant text-xs font-bold">
                            +{{ $stats['total_personnel'] - 3 }}
                        </div>
                    @endif
                </div>
                <span class="font-headline-lg text-[28px] text-on-surface font-black">{{ sprintf('%02d', $stats['total_personnel']) }}</span>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-12 gap-6 items-start mt-6">
        <section class="col-span-12 lg:col-span-8 space-y-4">
            <div class="flex items-center justify-between border-b border-surface-variant/30 pb-2">
                <h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">dynamic_feed</span>
                    Mission Critical Activities
                </h2>
                @if(auth()->user()?->hasPermission('manage_incidents'))
                    <a href="{{ route('incidents.create') }}" class="bg-primary text-on-primary text-body-sm font-bold py-1.5 px-4 rounded shadow-lg shadow-primary/10 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">add_alert</span> Declare Incident
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse ($recentActivities as $activity)
                    @php
                        $priorityClasses = match ($activity->priority) {
                            'high' => 'bg-warning-amber/20 text-warning-amber border-warning-amber/30',
                            'medium' => 'bg-primary/10 text-primary border-primary/20',
                            default => 'bg-success-emerald/10 text-success-emerald border-success-emerald/20',
                        };

                        $statusClasses = match ($activity->status) {
                            'completed' => 'text-success-emerald bg-success-emerald/10 border-success-emerald/20',
                            'in_progress' => 'text-primary bg-primary/10 border-primary/20',
                            default => 'text-warning-amber bg-warning-amber/10 border-warning-amber/20',
                        };
                    @endphp

                    @if($activity->priority === 'critical')
                        <a href="{{ route('activities.show', $activity) }}" class="bg-surface-container border border-danger-rose/50 rounded-lg p-5 group hover:border-danger-rose transition-all cursor-pointer">
                            <div class="flex justify-between items-start mb-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-danger-rose/20 text-danger-rose px-2 py-1 rounded text-[11px] font-black uppercase tracking-tight border border-danger-rose/30">
                                            P1
                                        </span>
                                        <h3 class="font-headline-md text-headline-md text-on-surface font-bold">{{ $activity->title }}</h3>
                                        <span class="bg-danger-rose/10 text-danger-rose px-2 py-0.5 rounded text-[10px] font-black uppercase border border-danger-rose/20">
                                            SLA At Risk
                                        </span>
                                    </div>
                                    <p class="text-on-surface-variant text-body-sm max-w-2xl leading-relaxed">{{ Str::limit($activity->description ?? $activity->title, 120) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono-data text-[11px] text-on-surface-variant">#ACT-{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <span class="text-danger-rose bg-danger-rose/10 px-2 py-0.5 rounded text-[10px] font-black inline-block mt-1 uppercase border border-danger-rose/20">
                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mb-4 border-t border-surface-variant/30 pt-4">
                                <div>
                                    <p class="font-label-caps text-[9px] text-on-surface-variant">Time Waiting</p>
                                    <p class="text-body-sm text-danger-rose font-bold">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                                <div>
                                    <p class="font-label-caps text-[9px] text-on-surface-variant">Assigned Lead</p>
                                    <div class="flex items-center gap-1">
                                        <div class="w-5 h-5 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container text-[10px] font-bold">
                                            {{ strtoupper(substr($activity->owner->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <p class="text-body-sm">{{ $activity->owner->name ?? 'Unassigned' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-label-caps text-[9px] text-on-surface-variant">Status</p>
                                    <p class="text-body-sm text-danger-rose font-bold">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</p>
                                </div>
                                <div>
                                    <p class="font-label-caps text-[9px] text-on-surface-variant">Region</p>
                                    <p class="text-body-sm text-on-surface-variant">Global</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-on-surface-variant">Collaborators:</span>
                                    <div class="flex -space-x-1">
                                        <div class="w-5 h-5 rounded-full bg-secondary-container border border-surface-container flex items-center justify-center text-on-secondary-container text-[8px] font-bold">JD</div>
                                        <div class="w-5 h-5 rounded-full bg-tertiary/30 border border-surface-container flex items-center justify-center text-tertiary text-[8px] font-bold">AK</div>
                                    </div>
                                </div>
                                <span class="text-danger-rose text-[11px] font-bold py-1.5 px-3 rounded bg-danger-rose/10 border border-danger-rose/20">Requires Review</span>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('activities.show', $activity) }}" class="bg-surface-container border border-surface-variant/50 rounded-lg p-5 group hover:border-primary/50 transition-all cursor-pointer">
                            <div class="flex justify-between items-start mb-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $priorityClasses }} px-2 py-1 rounded text-[11px] font-black uppercase tracking-tight border">
                                            {{ strtoupper($activity->priority) }}
                                        </span>
                                        <h3 class="font-headline-md text-headline-md text-on-surface font-bold">{{ $activity->title }}</h3>
                                    </div>
                                    <p class="text-on-surface-variant text-body-sm max-w-2xl leading-relaxed">{{ Str::limit($activity->description ?? $activity->title, 120) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono-data text-[11px] text-on-surface-variant">#ACT-{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <span class="{{ $statusClasses }} px-2 py-0.5 rounded text-[10px] font-black inline-block mt-1 uppercase border">
                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-6">
                                    <div class="flex flex-col">
                                        <span class="font-label-caps text-[9px] text-on-surface-variant">Created</span>
                                        <span class="font-mono-data text-body-sm">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex flex-col border-l border-surface-variant/30 pl-6">
                                        <span class="font-label-caps text-[9px] text-on-surface-variant">Owner</span>
                                        <div class="flex items-center gap-1">
                                            <div class="w-5 h-5 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container text-[10px] font-bold">
                                                {{ strtoupper(substr($activity->owner->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="text-body-sm">{{ $activity->owner->name ?? 'Unassigned' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <span class="text-primary text-[12px] font-bold py-1.5 px-3 rounded hover:bg-primary/10">Full Details</span>
                                    @if($activity->status === 'in_progress')
                                        <span class="bg-primary/10 text-primary text-[12px] font-bold py-1.5 px-3 rounded hover:bg-primary/20">Update Status</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <div class="bg-surface-container border border-surface-variant/50 rounded-lg p-12 text-center">
                        <span class="material-symbols-outlined text-5xl text-outline mb-3">task_alt</span>
                        <p class="text-on-surface-variant text-body-md">No active activities.</p>
                        <p class="text-on-surface-variant/60 text-body-sm mt-1">All tasks are completed or no tasks have been created yet.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="col-span-12 lg:col-span-4 space-y-4">
            <h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">analytics</span>
                General Health Stats
            </h2>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-surface-container p-3 border border-surface-variant/30 rounded text-center">
                    <p class="text-on-surface-variant text-[10px] font-label-caps">MTTR (24h)</p>
                    <p class="text-xl font-black text-success-emerald">{{ $mttr }}m</p>
                </div>
                <div class="bg-surface-container p-3 border border-surface-variant/30 rounded text-center">
                    <p class="text-on-surface-variant text-[10px] font-label-caps">SLA Compliance</p>
                    <p class="text-xl font-black text-primary">{{ $slaCompliance }}%</p>
                </div>
            </div>

            <div class="bg-surface-container-low border border-surface-variant/30 rounded-lg p-4 relative overflow-hidden h-[450px] flex flex-col">
                <div class="mb-4 flex items-center justify-between">
                    <span class="font-label-caps text-label-caps text-on-surface font-bold">Watch Log</span>
                    <span class="text-[10px] text-success-emerald flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-success-emerald"></span> LIVE</span>
                </div>
                <div class="absolute left-[27px] top-12 bottom-8 w-[1px] bg-slate-800"></div>
                <div class="flex-1 overflow-y-auto space-y-6 pr-2">
                    @forelse ($recentIncidents->take(5) as $incident)
                        @php
                            $incidentMarkerClass = $incident->status === 'resolved' ? 'bg-success-emerald' : 'bg-danger-rose';
                            $incidentTextClass = $incident->status === 'resolved' ? 'text-on-surface' : 'text-danger-rose';
                        @endphp
                        <div class="relative pl-10">
                            <div class="absolute left-[-4px] top-1 w-4 h-4 rounded-full {{ $incidentMarkerClass }} ring-4 ring-background border border-background z-10"></div>
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <p class="font-body-sm font-bold {{ $incidentTextClass }}">{{ $incident->status === 'resolved' ? 'Incident Resolved' : 'Incident Active' }}</p>
                                    <span class="font-mono-data text-[10px] text-on-surface-variant uppercase">{{ $incident->created_at->format('H:i') }} UTC</span>
                                </div>
                                <p class="text-body-sm text-on-surface-variant">{{ Str::limit($incident->title, 60) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="relative pl-10">
                            <div class="absolute left-[-4px] top-1 w-4 h-4 rounded-full bg-primary ring-4 ring-background border border-background z-10"></div>
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <p class="font-body-sm font-bold text-on-surface">System Online</p>
                                    <span class="font-mono-data text-[10px] text-on-surface-variant uppercase">NOW</span>
                                </div>
                                <p class="text-body-sm text-on-surface-variant">All systems operational.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="pt-4 border-t border-surface-variant/30 text-center">
                    <a href="{{ route('incidents.index') }}" class="text-primary font-label-caps text-label-caps uppercase hover:underline">View Full Transmission Log</a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
