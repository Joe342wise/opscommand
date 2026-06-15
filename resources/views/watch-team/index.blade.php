<x-layouts.app title="Watch Team - OpsCommand">
    <!-- Dynamic Content Grid -->
    <div class="space-y-6">
        <!-- Top: Shift Summary -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-surface-container p-3 border border-surface-variant flex flex-col justify-between h-24">
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Current Shift</p>
                <div class="flex items-end justify-between">
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $currentShift?->name ?? 'N/A' }}</span>
                    <span class="font-mono-data text-mono-data text-primary">{{ $currentShift?->start_time ?? '00:00' }} - {{ $currentShift?->end_time ?? '00:00' }}</span>
                </div>
            </div>
            <div class="bg-surface-container p-3 border border-surface-variant flex flex-col justify-between h-24">
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Personnel Online</p>
                <div class="flex items-end justify-between">
                    <span class="font-headline-lg text-headline-lg text-success-emerald">{{ $stats['online'] ?? 0 }} <small class="text-[14px] text-on-surface-variant">/ {{ $stats['total'] ?? 0 }}</small></span>
                    <div class="w-16 h-8 bg-success-emerald/10 rounded flex items-center justify-center">
                        <span class="text-[10px] text-success-emerald font-bold">+{{ $stats['available'] ?? 0 }} Available</span>
                    </div>
                </div>
            </div>
            <div class="bg-surface-container p-3 border border-surface-variant flex flex-col justify-between h-24">
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Active Incidents</p>
                <div class="flex items-end justify-between">
                    <span class="font-headline-lg text-headline-lg text-warning-amber">{{ str_pad($stats['active_incidents'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-xs text-on-surface-variant">{{ $stats['critical_incidents'] ?? 0 }} Critical</span>
                </div>
            </div>
            <div class="bg-surface-container p-3 border border-surface-variant flex flex-col justify-between h-24">
                <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Shift Completion</p>
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between items-center text-xs font-mono-data">
                        <span>{{ $stats['shift_completion'] ?? 0 }}%</span>
                        <span>{{ $stats['remaining_hours'] ?? 0 }}h Remaining</span>
                    </div>
                    <div class="w-full h-1.5 bg-surface-container-highest rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: {{ $stats['shift_completion'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Middle Main Section: Team Roster & Workload -->
        <div class="grid grid-cols-12 gap-4">
            <!-- Team Roster -->
            <section class="col-span-12 lg:col-span-8 bg-surface-container border border-surface-variant flex flex-col overflow-hidden">
                <div class="p-3 border-b border-surface-variant flex justify-between items-center bg-surface-container-high">
                    <h2 class="font-label-caps text-label-caps text-on-surface uppercase flex items-center">
                        <span class="material-symbols-outlined text-[16px] mr-2 text-primary">groups</span>
                        Team Roster — Watch Floor
                    </h2>
                    <div class="flex gap-2">
                        <button class="text-[10px] font-bold bg-surface-container-highest px-2 py-1 border border-outline-variant rounded hover:bg-surface-bright transition-colors uppercase">Filter: All</button>
                        <button class="text-[10px] font-bold bg-surface-container-highest px-2 py-1 border border-outline-variant rounded hover:bg-surface-bright transition-colors uppercase">Sort: Load</button>
                    </div>
                </div>
                <!-- High-Density Personnel Grid -->
                <div class="flex-1 overflow-y-auto p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 auto-rows-max">
                    @forelse ($personnel as $person)
                        <div class="bg-surface-container-low border border-slate-800 p-3 relative group transition-all hover:border-primary-container">
                            @if ($person->status === 'online')
                                <div class="absolute top-3 right-3">
                                    <span class="h-2 w-2 bg-success-emerald rounded-full block"></span>
                                </div>
                            @elseif ($person->status === 'on_call')
                                <div class="absolute top-0 right-0 p-1">
                                    <span class="flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-danger-rose opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-danger-rose"></span>
                                    </span>
                                </div>
                            @endif
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-10 h-10 rounded-sm bg-slate-800 flex items-center justify-center text-[14px] font-bold text-primary">
                                    {{ strtoupper(substr($person->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-headline-md text-[14px] leading-tight text-on-surface truncate">{{ $person->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-[10px] font-bold text-on-surface-variant uppercase">{{ $person->job_title ?? 'Personnel' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-1.5 py-0.5 {{ match($person->status ?? 'offline') { 'online' => 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20', 'on_call' => 'bg-danger-rose/10 text-danger-rose border border-danger-rose/20', 'on_break' => 'bg-surface-variant text-on-surface-variant border border-outline-variant', default => 'bg-surface-variant text-on-surface-variant border border-outline-variant' } }} text-[9px] font-bold uppercase rounded">
                                    {{ ucfirst(str_replace('_', ' ', $person->status ?? 'offline')) }}
                                </span>
                                <span class="text-[10px] text-on-surface-variant font-mono-data">{{ $person->team?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="space-y-1 border-t border-slate-800 pt-2">
                                @forelse ($person->activities->take(2) as $activity)
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="text-on-surface-variant">{{ Str::limit($activity->title, 25) }}</span>
                                        <span class="{{ $activity->status === 'completed' ? 'text-success-emerald' : 'text-primary' }} font-mono-data">{{ ucfirst($activity->status) }}</span>
                                    </div>
                                @empty
                                    <p class="text-[10px] italic text-on-surface-variant text-center py-2">No active activities</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2">groups</span>
                            <p class="text-sm text-slate-400">No personnel available.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Right Sidebar: Workload Distribution -->
            <section class="col-span-12 lg:col-span-4 flex flex-col gap-4 overflow-hidden">
                <!-- Activities per person Chart -->
                <div class="flex-1 bg-surface-container border border-surface-variant flex flex-col">
                    <div class="p-3 border-b border-surface-variant bg-surface-container-high">
                        <h3 class="font-label-caps text-label-caps text-on-surface-variant uppercase">Workload Intensity</h3>
                    </div>
                    <div class="flex-1 p-4 space-y-4">
                        <div class="space-y-3">
                            @foreach ($workload as $item)
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[10px] font-mono-data">
                                        <span>{{ $item['name'] }} ({{ $item['load'] }}%)</span>
                                        <span class="{{ $item['load'] > 80 ? 'text-danger-rose' : ($item['load'] > 60 ? 'text-primary' : 'text-success-emerald') }}">{{ $item['load'] }}%</span>
                                    </div>
                                    <div class="w-full h-2 bg-surface-container-highest rounded-full">
                                        <div class="h-full {{ $item['load'] > 80 ? 'bg-danger-rose' : ($item['load'] > 60 ? 'bg-primary' : 'bg-success-emerald') }} rounded-full" style="width: {{ $item['load'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Open Incidents Snapshot -->
                <div class="h-1/2 bg-surface-container border border-surface-variant flex flex-col">
                    <div class="p-3 border-b border-surface-variant bg-surface-container-high">
                        <h3 class="font-label-caps text-label-caps text-on-surface-variant uppercase">Open Incidents</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-left text-body-sm">
                            <thead class="text-label-caps text-[10px] text-on-surface-variant uppercase border-b border-surface-variant">
                                <tr>
                                    <th class="p-2 font-bold">ID</th>
                                    <th class="p-2 font-bold">Priority</th>
                                    <th class="p-2 font-bold text-right">Elapsed</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono-data text-[11px] divide-y divide-slate-800">
                                @forelse ($activeIncidents as $incident)
                                    <tr class="hover:bg-slate-900/50 cursor-pointer" onclick="window.location='{{ route('incidents.show', $incident) }}'">
                                        <td class="p-2 text-primary">INC-{{ str_pad($incident->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="p-2"><span class="px-1 {{ match($incident->severity) { 'P1' => 'bg-danger-rose/20 text-danger-rose', 'P2' => 'bg-warning-amber/20 text-warning-amber', 'P3' => 'bg-primary/20 text-primary', default => 'bg-surface-variant text-on-surface-variant' } }} rounded">{{ $incident->severity }}</span></td>
                                        <td class="p-2 text-right">{{ $incident->created_at->diffForHumans(null, true) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-on-surface-variant">No active incidents</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Bottom: Shift Timeline (Fixed) -->
    <footer class="fixed bottom-0 left-64 right-0 h-24 bg-surface-container-low border-t border-surface-variant z-40 p-4">
        <div class="max-w-full mx-auto h-full flex flex-col gap-2">
            <div class="flex justify-between items-center">
                <h4 class="font-label-caps text-label-caps text-on-surface-variant uppercase flex items-center">
                    <span class="material-symbols-outlined text-[14px] mr-1">timer</span>
                    Shift Coverage Forecast
                </h4>
                <span class="font-mono-data text-[10px] text-on-surface-variant">12:00 PM — 06:00 PM (UTC)</span>
            </div>
            <div class="relative flex-1 bg-surface-container rounded border border-slate-800 overflow-hidden">
                <!-- Timeline markers -->
                <div class="absolute inset-0 flex justify-between px-2 text-[9px] text-slate-500 font-mono-data pt-1 pointer-events-none">
                    <span>12:00</span>
                    <span>13:00</span>
                    <span>14:00</span>
                    <span>15:00</span>
                    <span>16:00</span>
                    <span>17:00</span>
                    <span>18:00</span>
                </div>
                <!-- Current Time Indicator -->
                <div class="absolute top-0 bottom-0 left-[22%] w-0.5 bg-primary shadow-[0_0_8px_rgba(195,192,255,0.5)] z-10"></div>
                <!-- Content Bars -->
                <div class="absolute inset-0 flex flex-col justify-center px-4 gap-1.5 mt-4">
                    <div class="h-3 w-full bg-surface-container-highest rounded-full flex overflow-hidden">
                        <div class="h-full bg-success-emerald/60 w-[45%] border-r border-background" title="Full Strength"></div>
                        <div class="h-full bg-warning-amber/40 w-[20%] border-r border-background" title="Lunch Overlap"></div>
                        <div class="h-full bg-success-emerald/60 w-[35%]" title="Full Strength"></div>
                    </div>
                    <div class="flex justify-between text-[9px] font-bold text-on-surface-variant uppercase">
                        <span>High Availability</span>
                        <span class="text-warning-amber">Reduced Staff (14:30)</span>
                        <span>Alpha-09 Prep (17:00)</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</x-layouts.app>
