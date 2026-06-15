<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Key Performance Indicators</h1>
                <p class="text-sm text-surface-bright mt-1">Track operational KPIs over time</p>
            </div>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">Back to Reports</a>
        </div>

        @if ($latestKpis->count() > 0)
            <div class="grid grid-cols-4 gap-4">
                @foreach ($latestKpis as $kpi => $value)
                    <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                        <p class="text-xs text-outline uppercase tracking-wide">{{ ucfirst(str_replace('_', ' ', $kpi)) }}</p>
                        <p class="text-2xl font-semibold text-on-surface mt-1">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">KPI Name</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Value</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Unit</th>
                        <th class="text-left text-xs font-medium text-surface-bright px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kpis as $kpi)
                        <tr class="border-b border-outline-variant hover:bg-surface-container-high transition-colors">
                            <td class="px-4 py-3 text-sm text-on-surface">{{ ucfirst(str_replace('_', ' ', $kpi->kpi_name)) }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-on-surface">{{ $kpi->value }}</td>
                            <td class="px-4 py-3 text-sm text-surface-bright">{{ $kpi->unit ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-surface-bright">{{ $kpi->snapshot_date->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-surface-bright">
                                <span class="material-symbols-outlined text-4xl text-surface-bright mb-2">analytics</span>
                                <p>No KPI snapshots recorded.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kpis->hasPages())
            <div class="mt-4">
                {{ $kpis->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
