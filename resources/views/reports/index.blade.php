<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Reports</h1>
                <p class="text-sm text-surface-bright mt-1">Generate and view operational reports</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.kpis') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">
                    View KPIs
                </a>
                <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">
                    Generate Report
                </a>
            </div>
        </div>

        <div class="space-y-3">
            @forelse ($reports as $report)
                <a href="{{ route('reports.show', $report) }}" class="block bg-surface-container rounded-xl p-4 border border-outline-variant hover:border-primary/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center text-sm">
                                <span class="material-symbols-outlined text-lg">assessment</span>
                            </span>
                            <div>
                                <h3 class="font-medium text-on-surface">{{ $report->title }}</h3>
                                <p class="text-sm text-surface-bright">{{ ucfirst($report->type) }} Report • {{ $report->created_by?->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $report->status === 'generated' ? 'bg-success-emerald/20 text-success-emerald' : ($report->status === 'exported' ? 'bg-primary/20 text-primary' : 'bg-surface-container-high text-surface-bright') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="text-sm text-surface-bright">{{ $report->created_at->format('M d, H:i') }}</span>
                            <span class="material-symbols-outlined text-surface-bright">chevron_right</span>
                        </div>
                    </div>
                    @if ($report->parameters)
                        <div class="mt-2 flex items-center gap-4">
                            <span class="text-xs text-surface-bright">From: {{ $report->parameters['date_from'] ?? '-' }}</span>
                            <span class="text-xs text-surface-bright">To: {{ $report->parameters['date_to'] ?? '-' }}</span>
                        </div>
                    @endif
                </a>
            @empty
                <div class="bg-surface-container rounded-xl p-12 border border-outline-variant text-center">
                    <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">assessment</span>
                    <p class="text-surface-bright">No reports generated yet.</p>
                </div>
            @endforelse
        </div>

        @if ($reports->hasPages())
            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
