<x-layouts.app title="Handover Board - OpsCommand">
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Handover Board</h2>
            <p class="text-on-surface-variant font-body-sm">Shift Alpha to Shift Beta Transition View</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('handovers.create') }}" class="bg-primary text-on-primary px-4 py-2 font-label-caps flex items-center gap-2 rounded-lg active:scale-[0.98] transition-transform shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-[18px]">add</span>
                New Handover
            </a>
        </div>
    </div>

    <!-- Shift Summary Metrics -->
    <div class="grid grid-cols-5 gap-3 mb-6">
        <div class="bg-surface-container-low border border-surface-variant p-3 rounded-lg flex flex-col gap-1">
            <p class="text-label-caps font-label-caps text-slate-400">COMPLETED</p>
            <div class="flex items-baseline gap-2">
                <span class="text-headline-lg text-success-emerald font-bold">{{ $stats['completed'] ?? 0 }}</span>
                <span class="text-mono-data text-slate-500">Handovers</span>
            </div>
        </div>
        <div class="bg-surface-container-low border border-surface-variant p-3 rounded-lg flex flex-col gap-1">
            <p class="text-label-caps font-label-caps text-slate-400">PENDING</p>
            <div class="flex items-baseline gap-2">
                <span class="text-headline-lg text-primary font-bold">{{ str_pad($stats['pending'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-mono-data text-slate-500">Active</span>
            </div>
        </div>
        <div class="bg-surface-container-low border border-surface-variant p-3 rounded-lg flex flex-col gap-1">
            <p class="text-label-caps font-label-caps text-slate-400">CRITICAL</p>
            <div class="flex items-baseline gap-2">
                <span class="text-headline-lg text-error font-bold">{{ str_pad($stats['critical'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-mono-data text-slate-500">Unresolved</span>
            </div>
        </div>
        <div class="bg-surface-container-low border border-surface-variant p-3 rounded-lg flex flex-col gap-1">
            <p class="text-label-caps font-label-caps text-slate-400">ESCALATED</p>
            <div class="flex items-baseline gap-2">
                <span class="text-headline-lg text-warning-amber font-bold">{{ str_pad($stats['escalated'] ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-mono-data text-slate-500">Tier 2+</span>
            </div>
        </div>
        <div class="bg-primary/10 border border-primary/20 p-3 rounded-lg flex flex-col gap-1">
            <p class="text-label-caps font-label-caps text-primary">SHIFT COMPLETION</p>
            <div class="flex items-baseline gap-2">
                <span class="text-headline-lg text-primary font-bold">{{ $stats['completion_rate'] ?? 0 }}%</span>
                <div class="flex-1 h-1 bg-slate-800 rounded-full overflow-hidden self-center ml-2">
                    <div class="h-full bg-primary" style="width: {{ $stats['completion_rate'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="flex gap-6">
        <!-- Pending Activities View -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Activity Cards -->
            <div class="space-y-4">
                @forelse ($handovers as $handover)
                    <article class="bg-surface-container-low border border-surface-variant rounded-lg p-card-padding hover:border-primary/50 transition-colors group" onclick="window.location='{{ route('handovers.show', $handover) }}'">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="px-2 py-1 {{ $handover->status === 'acknowledged' ? 'bg-success-emerald/10 border border-success-emerald/20 text-success-emerald' : ($handover->status === 'pending' ? 'bg-warning-amber/10 border border-warning-amber/20 text-warning-amber' : 'bg-surface-container-high border border-surface-variant text-slate-400') }} rounded font-label-caps flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">{{ $handover->status === 'acknowledged' ? 'check_circle' : ($handover->status === 'pending' ? 'pending' : 'draft') }}</span>
                                    {{ ucfirst($handover->status) }}
                                </div>
                                <h3 class="font-headline-md text-headline-md text-on-surface">{{ $handover->title }}</h3>
                            </div>
                            <div class="flex items-center gap-2 text-slate-400">
                                <span class="text-mono-data font-mono-data">ID: HND-{{ str_pad($handover->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="material-symbols-outlined group-hover:text-primary transition-colors cursor-pointer">open_in_new</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 mb-4">
                            <div class="col-span-8">
                                <div class="bg-slate-900/50 p-4 rounded-lg border border-slate-800">
                                    <p class="font-label-caps text-label-caps text-slate-500 mb-2">HANDOVER NOTES</p>
                                    <p class="font-headline-md text-headline-md text-slate-50 leading-relaxed italic">{{ Str::limit($handover->notes ?? 'No notes provided.', 200) }}</p>
                                </div>
                            </div>
                            <div class="col-span-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-label-caps font-label-caps text-on-surface-variant">FROM</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-body-sm font-body-sm">{{ $handover->fromShift?->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-label-caps font-label-caps text-on-surface-variant">TO</span>
                                        <span class="text-body-sm font-body-sm">{{ $handover->toShift?->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-label-caps font-label-caps text-on-surface-variant">CREATED</span>
                                        <span class="text-mono-data font-mono-data text-secondary">{{ $handover->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button class="flex-1 py-1 bg-surface-variant hover:bg-slate-700 text-on-surface text-label-caps rounded transition-colors">VIEW</button>
                                    <button class="px-3 py-1 bg-surface-variant hover:bg-slate-700 text-on-surface rounded transition-colors"><span class="material-symbols-outlined text-[18px]">more_horiz</span></button>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-surface-container-low border border-surface-variant rounded-lg p-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2">swap_horiz</span>
                        <p class="text-sm text-slate-400">No handovers found.</p>
                        <a href="{{ route('handovers.create') }}" class="mt-4 inline-flex items-center gap-2 bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold hover:opacity-90 transition-all active:scale-95">
                            <span class="material-symbols-outlined fill-icon">add</span>
                            Create First Handover
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($handovers->hasPages())
                <div class="mt-4">
                    {{ $handovers->links() }}
                </div>
            @endif
        </div>

        <!-- Handover Summary Sidebar -->
        <aside class="w-[420px] flex flex-col gap-gutter-desktop h-full">
            <!-- Handover Readiness Indicator -->
            @if ($stats['pending'] ?? 0 > 0)
                <div class="bg-warning-amber/10 border-2 border-warning-amber/30 rounded-lg p-4 flex items-center gap-4 shadow-xl">
                    <div class="w-12 h-12 bg-warning-amber/20 rounded-full flex items-center justify-center animate-pulse">
                        <span class="material-symbols-outlined text-warning-amber text-[32px]">pending_actions</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-headline-md text-warning-amber font-bold">Requires Review</p>
                        <p class="text-body-sm text-slate-400">{{ $stats['pending'] ?? 0 }} Pending Handover(s) require supervisor sign-off before handover.</p>
                    </div>
                </div>
            @endif

            <!-- Rich Workspace Sideboard -->
            <div class="bg-surface-container border border-surface-variant rounded-lg flex flex-col h-full shadow-2xl overflow-hidden">
                <div class="p-card-padding border-b border-surface-variant bg-surface-container-high/50">
                    <h3 class="font-headline-md text-headline-md text-primary mb-1">Handover Document</h3>
                    <p class="text-label-caps font-label-caps text-on-surface-variant uppercase tracking-widest">OUTGOING SHIFT REPORT</p>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-card-padding space-y-6">
                    <!-- Operational Summary -->
                    <section>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary text-[18px]">summarize</span>
                            <h4 class="font-label-caps text-label-caps text-primary">OPERATIONAL SUMMARY</h4>
                        </div>
                        <p class="text-body-sm font-body-sm text-on-surface leading-relaxed border-l-2 border-slate-800 pl-4">
                            Stable operations maintained across all sectors. Current shift activities are being tracked and documented. Security perimeter remains green.
                        </p>
                    </section>

                    <!-- Risks -->
                    <section>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-error text-[18px]">gpp_maybe</span>
                            <h4 class="font-label-caps text-label-caps text-error">RISKS & THREATS</h4>
                        </div>
                        <div class="space-y-3 pl-4">
                            <div class="p-2 bg-warning-amber/5 border border-warning-amber/20 rounded text-body-sm text-on-surface">
                                <span class="font-bold text-warning-amber">NOTE:</span> Review all pending escalations before shift change.
                            </div>
                        </div>
                    </section>

                    <!-- Outstanding Actions -->
                    <section>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-secondary text-[18px]">rule</span>
                            <h4 class="font-label-caps text-label-caps text-secondary">OUTSTANDING ACTIONS</h4>
                        </div>
                        <ul class="text-body-sm font-body-sm text-on-surface space-y-2 pl-4">
                            @forelse ($handovers->where('status', 'pending') as $item)
                                <li class="flex gap-2"><span class="text-secondary">•</span> {{ $item->title }}</li>
                            @empty
                                <li class="flex gap-2 text-slate-500"><span class="text-secondary">•</span> No outstanding actions</li>
                            @endforelse
                        </ul>
                    </section>

                    <!-- Recommendations -->
                    <section>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary-fixed text-[18px]">lightbulb</span>
                            <h4 class="font-label-caps text-label-caps text-primary-fixed">NEXT SHIFT RECOMMENDATIONS</h4>
                        </div>
                        <textarea class="w-full bg-slate-900/40 border border-slate-800 rounded p-3 text-body-sm text-slate-300 placeholder-slate-600 h-24 resize-none focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Enter strategy or guidance for the incoming supervisor..."></textarea>
                    </section>
                </div>
                <div class="p-card-padding border-t border-surface-variant bg-surface-container-high/50">
                    <button class="w-full bg-primary text-on-primary py-3 rounded-lg font-bold font-headline-md active:scale-[0.99] transition-transform flex items-center justify-center gap-2 hover:bg-primary-container">
                        <span class="material-symbols-outlined">verified</span>
                        Finalize & Handover Shift
                    </button>
                    <p class="text-center font-label-caps text-label-caps text-slate-500 mt-2">
                        Requires Lead Supervisor PIN to Lock
                    </p>
                </div>
            </div>
        </aside>
    </div>

    <!-- Floating Visualizer -->
    <div class="fixed bottom-6 right-[440px] bg-surface-container border border-surface-variant p-card-padding rounded-lg flex items-center gap-4 z-40 shadow-2xl backdrop-blur-md">
        <div>
            <p class="font-label-caps text-label-caps text-slate-500">SYSTEM STABILITY</p>
            <p class="font-mono-data text-mono-data text-success-emerald">99.98% NOMINAL</p>
        </div>
        <div class="w-24 h-10 flex items-end gap-1">
            <div class="w-2 bg-success-emerald/20 h-4 rounded-t"></div>
            <div class="w-2 bg-success-emerald/40 h-6 rounded-t"></div>
            <div class="w-2 bg-success-emerald/30 h-5 rounded-t"></div>
            <div class="w-2 bg-success-emerald/60 h-8 rounded-t"></div>
            <div class="w-2 bg-success-emerald/50 h-7 rounded-t"></div>
            <div class="w-2 bg-success-emerald h-10 rounded-t"></div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 2px; }
        article:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4); }
    </style>
</x-layouts.app>
