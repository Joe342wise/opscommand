<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">System Health</h1>
                <p class="text-sm text-surface-bright mt-1">Monitor service health and SLA compliance</p>
            </div>
            <a href="{{ route('services.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">
                Add Service
            </a>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Total Services</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Healthy</p>
                <p class="text-2xl font-semibold text-success-emerald mt-1">{{ $stats['healthy'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Warning</p>
                <p class="text-2xl font-semibold text-warning-amber mt-1">{{ $stats['warning'] }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Critical</p>
                <p class="text-2xl font-semibold text-danger-rose mt-1">{{ $stats['critical'] }}</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse ($services as $service)
                <a href="{{ route('services.show', $service) }}" class="block bg-surface-container rounded-xl p-4 border border-outline-variant hover:border-primary/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full {{ $service->status === 'healthy' ? 'bg-success-emerald/20 text-success-emerald' : ($service->status === 'warning' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-danger-rose/20 text-danger-rose') }} flex items-center justify-center text-sm">
                                <span class="material-symbols-outlined text-lg">{{ $service->status === 'healthy' ? 'check_circle' : ($service->status === 'warning' ? 'warning' : 'error') }}</span>
                            </span>
                            <div>
                                <h3 class="font-medium text-on-surface">{{ $service->name }}</h3>
                                <p class="text-sm text-surface-bright">{{ $service->category }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $service->status === 'healthy' ? 'bg-success-emerald/20 text-success-emerald' : ($service->status === 'warning' ? 'bg-warning-amber/20 text-warning-amber' : 'bg-danger-rose/20 text-danger-rose') }}">
                                {{ ucfirst($service->status) }}
                            </span>
                            <span class="material-symbols-outlined text-surface-bright">chevron_right</span>
                        </div>
                    </div>
                    @if ($service->description)
                        <p class="mt-2 text-sm text-surface-bright line-clamp-2">{{ Str::limit($service->description, 150) }}</p>
                    @endif
                    <div class="mt-2 flex items-center gap-4">
                        <span class="text-xs text-surface-bright">{{ $service->metrics()->count() }} metrics</span>
                        <span class="text-xs text-surface-bright">{{ $service->slaRecords()->where('is_met', true)->count() }}/{{ $service->slaRecords()->count() }} SLAs met</span>
                    </div>
                </a>
            @empty
                <div class="bg-surface-container rounded-xl p-12 border border-outline-variant text-center">
                    <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">monitor</span>
                    <p class="text-surface-bright">No services configured.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
