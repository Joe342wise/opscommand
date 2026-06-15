<x-layouts.app title="Reports - OpsCommand">
    <!-- TopNavBar -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Reporting Dashboard</h2>
            <p class="text-on-surface-variant font-body-sm">Operations analytics and performance insights</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center bg-surface-container-high rounded px-3 py-1.5 border border-outline-variant/30">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant mr-2">calendar_today</span>
                <span class="font-mono-data text-mono-data text-[12px]">Last 7 Days</span>
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant ml-2 cursor-pointer">expand_more</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reports.create') }}" class="bg-primary text-on-primary px-3 py-1.5 rounded flex items-center gap-2 transition-all hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span class="font-label-caps text-label-caps text-[10px]">NEW REPORT</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Executive Summary Panel -->
    <div class="bg-surface-container-high border border-primary/20 rounded-lg p-6 overflow-hidden relative mb-6">
        <div class="absolute top-0 right-0 p-4">
            <span class="px-3 py-1 rounded-full bg-success-emerald/10 text-success-emerald text-[11px] font-bold border border-success-emerald/30 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-success-emerald animate-pulse"></span> SYSTEM HEALTH: OPTIMAL
            </span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 space-y-3">
                <h3 class="font-headline-md text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">insights</span>
                    Executive Summary
                </h3>
                <p class="text-body-md text-on-surface-variant">Operations are currently healthy with an MTTR reduction of 14% this week. Primary risks identified in pending handovers regarding personnel turnover.</p>
            </div>
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-surface-container rounded border border-slate-800">
                    <p class="text-[10px] font-label-caps text-on-surface-variant mb-2">HOW HEALTHY ARE OPERATIONS?</p>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold text-success-emerald">98.4%</span>
                        <span class="text-[10px] text-on-surface-variant pb-1">Uptime</span>
                    </div>
                </div>
                <div class="p-4 bg-surface-container rounded border border-slate-800">
                    <p class="text-[10px] font-label-caps text-on-surface-variant mb-2">CURRENT RISKS</p>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-warning-amber">warning</span>
                        <span class="text-body-sm font-bold">{{ $kpis['escalation_rate'] ?? 0 }}% Escalation Rate</span>
                    </div>
                </div>
                <div class="p-4 bg-surface-container rounded border border-slate-800">
                    <p class="text-[10px] font-label-caps text-on-surface-variant mb-2">MANAGEMENT ATTENTION</p>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-danger-rose">priority_high</span>
                        <span class="text-body-sm font-bold">{{ $kpis['overdue_rate'] ?? 0 }}% Overdue Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational KPIs Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-container border border-slate-800 rounded-lg p-3 group hover:border-primary/50 transition-colors">
            <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Total Activities</p>
            <h3 class="text-2xl font-bold font-mono-data">{{ $kpis['total_activities'] ?? 0 }}</h3>
            <div class="mt-2 flex items-center gap-1 text-success-emerald text-[11px] font-bold">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>{{ $kpis['completion_rate'] ?? 0 }}% completion</span>
            </div>
        </div>
        <div class="bg-surface-container border border-slate-800 rounded-lg p-3 group hover:border-primary/50 transition-colors">
            <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Total Incidents</p>
            <h3 class="text-2xl font-bold font-mono-data">{{ $kpis['total_incidents'] ?? 0 }}</h3>
            <div class="mt-2 flex items-center gap-1 text-danger-rose text-[11px] font-bold">
                <span class="material-symbols-outlined text-[14px]">warning</span>
                <span>{{ $kpis['incident_count'] ?? 0 }} unresolved</span>
            </div>
        </div>
        <div class="bg-surface-container border border-slate-800 rounded-lg p-3 group hover:border-primary/50 transition-colors">
            <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Active Escalations</p>
            <h3 class="text-2xl font-bold font-mono-data text-primary">{{ $kpis['active_escalations'] ?? 0 }}</h3>
            <div class="mt-2 flex items-center gap-1 text-warning-amber text-[11px] font-bold">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>{{ $kpis['escalation_rate'] ?? 0 }}% rate</span>
            </div>
        </div>
        <div class="bg-surface-container border border-slate-800 rounded-lg p-3 group hover:border-primary/50 transition-colors">
            <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Handovers</p>
            <h3 class="text-2xl font-bold font-mono-data">{{ $kpis['total_handovers'] ?? 0 }}</h3>
            <div class="mt-2 flex items-center gap-1 text-success-emerald text-[11px] font-bold">
                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                <span>{{ $kpis['handover_completion'] ?? 0 }}% acknowledged</span>
            </div>
        </div>
        <div class="bg-surface-container border border-slate-800 rounded-lg p-3 group hover:border-primary/50 transition-colors">
            <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Overdue Rate</p>
            <h3 class="text-2xl font-bold font-mono-data text-danger-rose">{{ $kpis['overdue_rate'] ?? 0 }}%</h3>
            <div class="mt-2 flex items-center gap-1 text-on-surface-variant text-[11px] font-bold">
                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                <span>Within target (&lt;1%)</span>
            </div>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <div class="bg-surface-container border border-slate-800 rounded-lg overflow-hidden">
        <div class="p-3 border-b border-slate-800 flex justify-between items-center">
            <h4 class="font-headline-md">Recent Reports</h4>
            <a href="{{ route('reports.index') }}" class="text-primary font-label-caps text-label-caps hover:underline">VIEW ALL</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50">
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800">REPORT ID</th>
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800">TYPE</th>
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800">DATE RANGE</th>
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800">GENERATED</th>
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800">STATUS</th>
                        <th class="px-4 py-3 font-label-caps text-[10px] text-on-surface-variant border-b border-slate-800 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-surface-variant/30 transition-colors group">
                            <td class="px-4 py-3 font-mono-data text-[12px]">RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-body-sm">{{ ucfirst($report->type) }}</td>
                            <td class="px-4 py-3 text-body-sm text-on-surface-variant">{{ $report->start_date?->format('M d') ?? '—' }} - {{ $report->end_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono-data text-[11px]">{{ $report->created_at->format('M d, H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $report->status === 'completed' ? 'bg-success-emerald/10 text-success-emerald border border-success-emerald/20' : 'bg-warning-amber/10 text-warning-amber border border-warning-amber/20' }} uppercase tracking-tight">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('reports.show', $report) }}" class="opacity-0 group-hover:opacity-100 material-symbols-outlined text-primary hover:text-white transition-all">open_in_new</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline mb-2">analytics</span>
                                <p class="text-sm text-slate-400">No reports generated yet.</p>
                                <a href="{{ route('reports.create') }}" class="mt-4 inline-flex items-center gap-2 bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold hover:opacity-90 transition-all active:scale-95">
                                    <span class="material-symbols-outlined fill-icon">add</span>
                                    Generate First Report
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Action Button -->
    <a href="{{ route('reports.create') }}" class="fixed bottom-8 right-8 w-14 h-14 bg-primary-container text-on-primary-container rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-50 group">
        <span class="material-symbols-outlined text-[28px] font-bold">add</span>
        <span class="absolute right-16 bg-surface-container border border-outline-variant px-3 py-1 rounded text-[11px] font-label-caps whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">NEW REPORT</span>
    </a>

    <style>
        .fill-icon { font-variation-settings: 'FILL' 1; }
    </style>
</x-layouts.app>
