<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $service->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $service->category }} • {{ $service->description ?? 'No description' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('services.update', $service) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        <option value="healthy" {{ $service->status === 'healthy' ? 'selected' : '' }}>Healthy</option>
                        <option value="warning" {{ $service->status === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="critical" {{ $service->status === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Status</p>
                <p class="text-2xl font-semibold mt-1 {{ $service->status === 'healthy' ? 'text-success-emerald' : ($service->status === 'warning' ? 'text-warning-amber' : 'text-danger-rose') }}">
                    {{ ucfirst($service->status) }}
                </p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Metrics</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">{{ $service->metrics()->count() }}</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">SLA Compliance</p>
                @php $slaMet = $service->slaRecords()->where('is_met', true)->count(); @endphp
                <p class="text-2xl font-semibold text-on-surface mt-1">{{ $slaMet }}/{{ $service->slaRecords()->count() }}</p>
            </div>
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <h2 class="text-lg font-medium text-on-surface mb-4">Metrics</h2>
            @if ($service->metrics->count() > 0)
                <div class="space-y-2">
                    @foreach ($service->metrics as $metric)
                        <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                            <span class="text-sm text-on-surface">{{ $metric->metric_name }}</span>
                            <span class="text-sm font-medium text-on-surface">{{ $metric->metric_value }} {{ $metric->unit }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-surface-bright">No metrics recorded.</p>
            @endif
        </div>

        <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
            <h2 class="text-lg font-medium text-on-surface mb-4">SLA Records</h2>
            @if ($service->slaRecords->count() > 0)
                <div class="space-y-2">
                    @foreach ($service->slaRecords as $sla)
                        <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg border border-outline-variant">
                            <div>
                                <p class="text-sm font-medium text-on-surface">{{ $sla->sla_name }}</p>
                                <p class="text-xs text-surface-bright">{{ $sla->period_start->format('M d') }} - {{ $sla->period_end->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-on-surface">{{ $sla->actual_value ?? '-' }} / {{ $sla->target_value }} {{ $sla->unit }}</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $sla->is_met ? 'bg-success-emerald/20 text-success-emerald' : 'bg-danger-rose/20 text-danger-rose' }}">
                                    {{ $sla->is_met ? 'Met' : 'Breached' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-surface-bright">No SLA records.</p>
            @endif
        </div>
    </div>
</x-layouts.app>
