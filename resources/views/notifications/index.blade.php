<x-layouts.app title="Notifications - OpsCommand">
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-2">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Notification Center</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Reviewing {{ $alerts->total() + $notifications->total() }} active alerts across operational systems.</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="px-4 py-2 border border-outline-variant hover:bg-surface-variant transition-colors flex items-center gap-2 rounded-lg">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                    <span class="font-label-caps text-label-caps">Mark All As Read</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Filter Sidebar -->
        <div class="col-span-3 space-y-4">
            <div class="bg-surface-container rounded-lg border border-outline-variant p-3">
                <h3 class="font-label-caps text-label-caps text-primary mb-4">Alert Classification</h3>
                <nav class="space-y-1">
                    <a href="{{ route('notifications.index') }}" class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-variant transition-colors group {{ !request('type') ? 'bg-primary/10 border-l-2 border-primary' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-primary">campaign</span>
                            <span class="font-body-sm text-body-sm {{ !request('type') ? 'text-primary font-bold' : 'text-on-surface-variant group-hover:text-on-surface' }}">All Notifications</span>
                        </div>
                        <span class="font-mono-data text-mono-data {{ !request('type') ? 'bg-primary text-on-primary px-1.5 rounded' : 'text-on-surface-variant opacity-50' }}">{{ $alerts->total() + $notifications->total() }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'critical']) }}" class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-variant transition-colors group {{ request('type') === 'critical' ? 'bg-primary/10 border-l-2 border-primary' : '' }}">
                        <div class="flex items-center gap-3 {{ request('type') === 'critical' ? 'text-on-surface' : 'text-on-surface-variant group-hover:text-on-surface' }}">
                            <span class="material-symbols-outlined text-[18px] text-danger-rose fill-icon">error</span>
                            <span class="font-body-sm text-body-sm">Critical Alerts</span>
                        </div>
                        <span class="font-mono-data text-mono-data text-on-surface-variant opacity-50">{{ $alerts->where('severity', 'critical')->count() }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'warning']) }}" class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-variant transition-colors group {{ request('type') === 'warning' ? 'bg-primary/10 border-l-2 border-primary' : '' }}">
                        <div class="flex items-center gap-3 {{ request('type') === 'warning' ? 'text-on-surface' : 'text-on-surface-variant group-hover:text-on-surface' }}">
                            <span class="material-symbols-outlined text-[18px] text-warning-amber fill-icon">warning</span>
                            <span class="font-body-sm text-body-sm">Operational Warnings</span>
                        </div>
                        <span class="font-mono-data text-mono-data text-on-surface-variant opacity-50">{{ $alerts->where('severity', 'warning')->count() }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'info']) }}" class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-variant transition-colors group {{ request('type') === 'info' ? 'bg-primary/10 border-l-2 border-primary' : '' }}">
                        <div class="flex items-center gap-3 {{ request('type') === 'info' ? 'text-on-surface' : 'text-on-surface-variant group-hover:text-on-surface' }}">
                            <span class="material-symbols-outlined text-[18px] text-secondary">info</span>
                            <span class="font-body-sm text-body-sm">Informational Updates</span>
                        </div>
                        <span class="font-mono-data text-mono-data text-on-surface-variant opacity-50">{{ $notifications->count() }}</span>
                    </a>
                </nav>
            </div>

            <div class="bg-surface-container rounded-lg border border-outline-variant p-3">
                <h3 class="font-label-caps text-label-caps text-primary mb-4">Service Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Core Engine</span>
                        <span class="px-2 py-0.5 bg-success-emerald/10 text-success-emerald font-mono-data text-[10px] rounded border border-success-emerald/20">STABLE</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Handover API</span>
                        <span class="px-2 py-0.5 bg-success-emerald/10 text-success-emerald font-mono-data text-[10px] rounded border border-success-emerald/20">STABLE</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Audit Sync</span>
                        <span class="px-2 py-0.5 bg-success-emerald/10 text-success-emerald font-mono-data text-[10px] rounded border border-success-emerald/20">STABLE</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Canvas -->
        <div class="col-span-9 space-y-6 max-h-[calc(100vh-12rem)] overflow-y-auto pr-2">
            <!-- Critical Alerts Section -->
            @if ($alerts->where('severity', 'critical')->count() > 0)
                <section>
                    <div class="flex items-center gap-2 mb-3 px-1">
                        <span class="material-symbols-outlined text-danger-rose text-[20px]">priority_high</span>
                        <h3 class="font-label-caps text-label-caps text-on-surface tracking-[0.1em]">CRITICAL_ALERTS</h3>
                        <div class="flex-1 h-px bg-gradient-to-r from-danger-rose/30 to-transparent ml-2"></div>
                    </div>
                    <div class="space-y-4">
                        @foreach ($alerts->where('severity', 'critical') as $alert)
                            <div class="bg-slate-900 border border-danger-rose/30 rounded-lg p-3 flex gap-4 hover:border-danger-rose/60 transition-all cursor-pointer relative overflow-hidden group">
                                <div class="absolute inset-y-0 left-0 w-1 bg-danger-rose"></div>
                                <div class="w-10 h-10 rounded bg-danger-rose/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-danger-rose fill-icon">emergency_home</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-headline-md text-body-md font-bold text-on-surface">{{ $alert->title }}</h4>
                                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $alert->message }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-mono-data text-mono-data text-danger-rose block">{{ $alert->created_at->diffForHumans() }}</span>
                                            <span class="font-label-caps text-[9px] text-on-surface-variant uppercase">Time Since Event</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center gap-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-body-sm text-on-surface-variant">person_search</span>
                                            <span class="font-mono-data text-[11px] text-on-surface-variant">ASSIGNED: {{ $alert->assignee?->name ?? 'Unassigned' }}</span>
                                        </div>
                                        <button class="ml-auto bg-danger-rose text-white font-label-caps text-label-caps px-3 py-1 rounded hover:opacity-90 transition-opacity">Acknowledge</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Warning Alerts Section -->
            @if ($alerts->where('severity', 'warning')->count() > 0)
                <section>
                    <div class="flex items-center gap-2 mb-3 px-1 mt-6">
                        <span class="material-symbols-outlined text-warning-amber text-[20px]">warning</span>
                        <h3 class="font-label-caps text-label-caps text-on-surface tracking-[0.1em]">OPERATIONAL_WARNINGS</h3>
                        <div class="flex-1 h-px bg-gradient-to-r from-warning-amber/30 to-transparent ml-2"></div>
                    </div>
                    <div class="space-y-4">
                        @foreach ($alerts->where('severity', 'warning') as $alert)
                            <div class="bg-slate-900 border border-warning-amber/30 rounded-lg p-3 flex gap-4 hover:border-warning-amber/60 transition-all cursor-pointer relative overflow-hidden group">
                                <div class="absolute inset-y-0 left-0 w-1 bg-warning-amber"></div>
                                <div class="w-10 h-10 rounded bg-warning-amber/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-warning-amber fill-icon">swap_horiz</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-headline-md text-body-md font-bold text-on-surface">{{ $alert->title }}</h4>
                                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $alert->message }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-mono-data text-mono-data text-warning-amber block">{{ $alert->created_at->diffForHumans() }}</span>
                                            <span class="font-label-caps text-[9px] text-on-surface-variant uppercase">Time Since Event</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Operational Notifications Section -->
            <section>
                <div class="flex items-center gap-2 mb-3 px-1 mt-6">
                    <span class="material-symbols-outlined text-primary text-[20px]">settings_accessibility</span>
                    <h3 class="font-label-caps text-label-caps text-on-surface tracking-[0.1em]">OPERATIONAL_STREAM</h3>
                    <div class="flex-1 h-px bg-gradient-to-r from-primary/30 to-transparent ml-2"></div>
                </div>
                <div class="space-y-4">
                    @forelse ($notifications as $notification)
                        <div class="bg-surface-container border border-outline-variant rounded-lg p-3 flex gap-4 hover:bg-surface-container-high transition-all cursor-pointer {{ $notification->read_at ? 'opacity-70 grayscale-[0.5]' : '' }}">
                            <div class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary">{{ match($notification->type ?? 'info') { 'success' => 'check_circle', 'warning' => 'warning', 'error' => 'error', default => 'info' } }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-body-md text-body-md font-semibold text-on-surface">{{ $notification->title }}</h4>
                                    <span class="font-mono-data text-[11px] text-on-surface-variant">{{ $notification->created_at->format('H:i') }}</span>
                                </div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $notification->message }}</p>
                            </div>
                            @if ($notification->read_at)
                                <div class="shrink-0 flex flex-col items-end">
                                    <span class="material-symbols-outlined text-secondary text-[16px]">done_all</span>
                                    <span class="font-label-caps text-[8px] mt-1">READ</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center border-2 border-dashed border-outline-variant rounded-lg">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2">notifications_off</span>
                            <p class="font-body-sm text-on-surface-variant">No active operational notifications.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <style>
        .fill-icon { font-variation-settings: 'FILL' 1; }
    </style>
</x-layouts.app>
