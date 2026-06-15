<x-layouts.app title="System Health - OpsCommand">
    <!-- Top Section: Health Summary Cards -->
    <section class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-container border border-surface-variant p-3 rounded-lg flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Overall Health</span>
                <span class="material-symbols-outlined text-success-emerald">verified</span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="font-headline-lg text-headline-lg text-primary">{{ $healthScore ?? 98.4 }}</span>
                <span class="font-mono-data text-mono-data text-success-emerald">+0.2%</span>
            </div>
            <div class="w-full bg-slate-800 h-1 mt-2 rounded-full overflow-hidden">
                <div class="bg-success-emerald h-full" style="width: {{ $healthScore ?? 98.4 }}%"></div>
            </div>
        </div>
        <div class="bg-surface-container border border-surface-variant p-3 rounded-lg flex flex-col">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Healthy Services</span>
            <div class="mt-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-success-emerald/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-success-emerald">check_circle</span>
                </div>
                <div>
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['healthy'] ?? 0 }}</span>
                    <p class="text-[10px] text-on-surface-variant">OPERATIONAL</p>
                </div>
            </div>
        </div>
        <div class="bg-surface-container border border-surface-variant p-3 rounded-lg flex flex-col">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Degraded State</span>
            <div class="mt-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-warning-amber/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-warning-amber">warning</span>
                </div>
                <div>
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['warning'] ?? 0 }}</span>
                    <p class="text-[10px] text-on-surface-variant">INVESTIGATING</p>
                </div>
            </div>
        </div>
        <div class="bg-surface-container border border-surface-variant p-3 rounded-lg flex flex-col">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Critical Failures</span>
            <div class="mt-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-danger-rose/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-danger-rose">error</span>
                </div>
                <div>
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['critical'] ?? 0 }}</span>
                    <p class="text-[10px] text-on-surface-variant">SYSTEM WIDE</p>
                </div>
            </div>
        </div>
        <div class="bg-surface-container border border-surface-variant p-3 rounded-lg flex flex-col">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Active Incidents</span>
            <div class="mt-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-primary-container/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">emergency_home</span>
                </div>
                <div>
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $stats['active_incidents'] ?? 0 }}</span>
                    <p class="text-[10px] text-on-surface-variant">LAST 24H</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Middle Section: Grid and Incidents -->
    <div class="grid grid-cols-12 gap-6 mb-6">
        <!-- Service Status Grid -->
        <div class="col-span-12 lg:col-span-8 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-headline-md text-headline-md text-on-surface">Service Matrix</h3>
                <div class="flex gap-2">
                    <a href="{{ route('services.create') }}" class="bg-primary text-on-primary px-3 py-1 rounded text-[11px] font-bold hover:opacity-90 transition-colors">ADD SERVICE</a>
                </div>
            </div>
            <div class="bg-surface-container border border-surface-variant rounded-lg overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-highest/50 border-b border-surface-variant">
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">SERVICE IDENTIFIER</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">STATUS</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">UPTIME</th>
                            <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant text-right">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant/30">
                        @forelse ($services as $service)
                            <tr class="hover:bg-surface-variant/20 group transition-colors cursor-pointer" onclick="window.location='{{ route('services.show', $service) }}'">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full {{ match($service->health_status) { 'healthy' => 'bg-success-emerald shadow-[0_0_8px_rgba(16,185,129,0.5)]', 'warning' => 'bg-warning-amber shadow-[0_0_8px_rgba(245,158,11,0.5)]', 'critical' => 'bg-danger-rose shadow-[0_0_8px_rgba(244,63,94,0.5)]', default => 'bg-surface-variant' } }}"></div>
                                        <span class="font-body-md text-body-md font-semibold">{{ $service->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ match($service->health_status) { 'healthy' => 'bg-success-emerald/10 text-success-emerald', 'warning' => 'bg-warning-amber/10 text-warning-amber', 'critical' => 'bg-danger-rose/10 text-danger-rose', default => 'bg-surface-variant text-on-surface-variant' } }} uppercase">
                                        {{ ucfirst($service->health_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-mono-data text-mono-data text-on-surface-variant">{{ $service->uptime ?? '—' }}</td>
                                <td class="px-4 py-4 text-right font-mono-data text-mono-data {{ ($service->sla_compliance ?? 0) >= 99 ? 'text-primary' : 'text-warning-amber' }}">{{ $service->sla_compliance ?? 0 }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <span class="material-symbols-outlined text-4xl text-outline mb-2">health_and_safety</span>
                                    <p class="text-sm text-slate-400">No services configured.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Panel: Active Incidents List -->
        <div class="col-span-12 lg:col-span-4 flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-headline-md text-headline-md text-on-surface">Active Incidents</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-danger-rose/10 text-danger-rose">{{ $activeIncidents->count() }} UNRESOLVED</span>
            </div>
            <div class="bg-surface-container border border-surface-variant rounded-lg p-4 flex-1 space-y-4">
                @forelse ($activeIncidents as $incident)
                    <div class="border-l-2 {{ match($incident->severity) { 'P1' => 'border-danger-rose', 'P2' => 'border-warning-amber', default => 'border-primary' } }} pl-4 py-1">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-body-md text-body-md font-bold text-on-surface">INC-{{ str_pad($incident->id, 4, '0', STR_PAD_LEFT) }}: {{ Str::limit($incident->title, 30) }}</h4>
                            <span class="font-mono-data text-[10px] text-on-surface-variant bg-surface-variant px-2 py-0.5 rounded">T+{{ $incident->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-tight">{{ Str::limit($incident->description, 80) }}</p>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[14px] text-primary">sync</span>
                                <span class="font-label-caps text-[10px] text-on-surface-variant">INVESTIGATING</span>
                            </div>
                            <a href="{{ route('incidents.show', $incident) }}" class="font-label-caps text-[10px] text-primary hover:underline uppercase tracking-widest">View Thread</a>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <div class="h-[1px] bg-surface-variant/30"></div>
                    @endif
                @empty
                    <div class="py-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2">check_circle</span>
                        <p class="text-sm text-slate-400">No active incidents.</p>
                    </div>
                @endforelse

                <!-- Atmosphere visual filler -->
                <div class="mt-auto pt-6 opacity-30">
                    <div class="flex items-center justify-between font-label-caps text-[9px] text-on-surface-variant mb-2">
                        <span>SYSTEM LOAD (CPU)</span>
                        <span>42.8%</span>
                    </div>
                    <div class="h-1 bg-surface-variant rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: 42%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Availability Trends -->
    <section class="space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-headline-md text-headline-md text-on-surface">Availability Trends</h3>
            <span class="font-label-caps text-label-caps text-on-surface-variant">AGGREGATE DATA 7-NODE CLUSTER</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-container border border-surface-variant p-4 rounded-lg relative overflow-hidden group">
                <div class="flex justify-between items-end mb-6 relative z-10">
                    <div>
                        <span class="font-label-caps text-label-caps text-on-surface-variant block mb-1">LAST 24 HOURS</span>
                        <span class="font-headline-lg text-headline-lg text-on-surface">99.88%</span>
                    </div>
                    <span class="material-symbols-outlined text-success-emerald opacity-50">trending_up</span>
                </div>
                <div class="relative h-16 w-full">
                    <svg class="w-full h-full stroke-primary fill-none stroke-[1.5] filter drop-shadow-[0_0_8px_rgba(195,192,255,0.3)]" viewBox="0 0 100 20">
                        <path d="M0,15 L10,12 L20,18 L30,5 L40,14 L50,12 L60,16 L70,8 L80,10 L90,14 L100,10"></path>
                    </svg>
                </div>
            </div>
            <div class="bg-surface-container border border-surface-variant p-4 rounded-lg relative overflow-hidden group">
                <div class="flex justify-between items-end mb-6 relative z-10">
                    <div>
                        <span class="font-label-caps text-label-caps text-on-surface-variant block mb-1">LAST 7 DAYS</span>
                        <span class="font-headline-lg text-headline-lg text-on-surface">99.45%</span>
                    </div>
                    <span class="material-symbols-outlined text-warning-amber opacity-50">trending_flat</span>
                </div>
                <div class="relative h-16 w-full">
                    <svg class="w-full h-full stroke-secondary fill-none stroke-[1.5] filter drop-shadow-[0_0_8px_rgba(78,222,163,0.3)]" viewBox="0 0 100 20">
                        <path d="M0,10 L10,12 L20,8 L30,9 L40,11 L50,10 L60,12 L70,11 L80,10 L90,9 L100,10"></path>
                    </svg>
                </div>
            </div>
            <div class="bg-surface-container border border-surface-variant p-4 rounded-lg relative overflow-hidden group">
                <div class="flex justify-between items-end mb-6 relative z-10">
                    <div>
                        <span class="font-label-caps text-label-caps text-on-surface-variant block mb-1">LAST 30 DAYS</span>
                        <span class="font-headline-lg text-headline-lg text-on-surface">99.91%</span>
                    </div>
                    <span class="material-symbols-outlined text-success-emerald opacity-50">trending_up</span>
                </div>
                <div class="relative h-16 w-full">
                    <svg class="w-full h-full stroke-primary fill-none stroke-[1.5] filter drop-shadow-[0_0_8px_rgba(195,192,255,0.3)]" viewBox="0 0 100 20">
                        <path d="M0,18 L10,15 L20,12 L30,10 L40,8 L50,6 L60,5 L70,4 L80,3 L90,2 L100,1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <style>
        .sparkline-animate { stroke-dasharray: 100; stroke-dashoffset: 100; animation: dash 2s ease-in-out forwards; }
        @keyframes dash { to { stroke-dashoffset: 0; } }
    </style>
</x-layouts.app>
