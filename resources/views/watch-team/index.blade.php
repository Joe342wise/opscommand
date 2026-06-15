<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Watch Team</h1>
                <p class="text-sm text-surface-bright mt-1">Active operational personnel and team readiness</p>
            </div>
            @if ($currentShift)
                <div class="bg-primary/10 border border-primary/30 rounded-lg px-4 py-2">
                    <p class="text-xs text-primary font-medium">Current Shift</p>
                    <p class="text-sm text-on-surface">{{ $currentShift->name }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-6 gap-4">
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Personnel</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">{{ $stats['total_personnel'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">On Shift</p>
                <p class="text-2xl font-semibold text-primary mt-1">{{ $stats['on_shift'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Activities</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">{{ $stats['total_activities'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Completed</p>
                <p class="text-2xl font-semibold text-success-emerald mt-1">{{ $stats['completed_activities'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Incidents</p>
                <p class="text-2xl font-semibold text-danger-rose mt-1">{{ $stats['active_incidents'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Escalations</p>
                <p class="text-2xl font-semibold text-warning-amber mt-1">{{ $stats['pending_escalations'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-4">
                @foreach ($teams as $teamName => $team)
                    <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
                        <div class="p-4 border-b border-outline-variant">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-on-surface">{{ $teamName }}</h2>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs text-surface-bright">{{ $team['members']->count() }} members</span>
                                    <span class="text-xs text-surface-bright">{{ $team['completed_activities'] }}/{{ $team['total_activities'] }} activities</span>
                                </div>
                            </div>
                        </div>
                        <div class="divide-y divide-outline-variant">
                            @foreach ($team['members'] as $member)
                                <div class="p-4 hover:bg-surface-container-high transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center text-sm font-medium">
                                                {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-on-surface">{{ $member->first_name }} {{ $member->last_name }}</p>
                                                <p class="text-xs text-surface-bright">{{ $member->position ?? 'Support Personnel' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-right">
                                                <p class="text-xs text-surface-bright">Activities</p>
                                                <p class="text-sm font-medium text-on-surface">{{ $member->assigned_activities }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-surface-bright">Incidents</p>
                                                <p class="text-sm font-medium {{ $member->active_incidents > 0 ? 'text-danger-rose' : 'text-on-surface' }}">{{ $member->active_incidents }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-surface-bright">Escalations</p>
                                                <p class="text-sm font-medium {{ $member->owned_escalations > 0 ? 'text-warning-amber' : 'text-on-surface' }}">{{ $member->owned_escalations }}</p>
                                            </div>
                                            <div class="w-24">
                                                @php $completionRate = $member->total_activities > 0 ? ($member->completed_activities / $member->total_activities) * 100 : 0; @endphp
                                                <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                                    <div class="bg-success-emerald h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
                                                </div>
                                                <p class="text-xs text-outline text-right mt-0.5">{{ number_format($completionRate, 0) }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-4">
                    <h2 class="text-sm font-medium text-on-surface mb-3">Team Workload Summary</h2>
                    <div class="space-y-3">
                        @foreach ($teams as $teamName => $team)
                            <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-on-surface">{{ $teamName }}</span>
                                    <span class="text-xs text-surface-bright">{{ $team['members']->count() }}</span>
                                </div>
                                <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                    @php $teamRate = $team['total_activities'] > 0 ? ($team['completed_activities'] / $team['total_activities']) * 100 : 0; @endphp
                                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ $teamRate }}%"></div>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-outline">{{ $team['completed_activities'] }}/{{ $team['total_activities'] }}</span>
                                    <span class="text-xs text-outline">{{ number_format($teamRate, 0) }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-4">
                    <h2 class="text-sm font-medium text-on-surface mb-3">Upcoming Shifts</h2>
                    @if ($upcomingShifts->count() > 0)
                        <div class="space-y-2">
                            @foreach ($upcomingShifts as $shift)
                                <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                                    <p class="text-sm text-on-surface">{{ $shift->name }}</p>
                                    <p class="text-xs text-surface-bright">{{ $shift->start_time->format('M d, H:i') }} - {{ $shift->end_time->format('H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-surface-bright">No upcoming shifts.</p>
                    @endif
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-4">
                    <h2 class="text-sm font-medium text-on-surface mb-3">Operational Readiness</h2>
                    @php $readinessRate = $stats['total_personnel'] > 0 ? ($stats['on_shift'] / $stats['total_personnel']) * 100 : 0; @endphp
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full border-4 border-primary mx-auto flex items-center justify-center">
                            <span class="text-xl font-semibold text-on-surface">{{ number_format($readinessRate, 0) }}%</span>
                        </div>
                        <p class="text-xs text-surface-bright mt-2">{{ $stats['on_shift'] }} of {{ $stats['total_personnel'] }} personnel on shift</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
