<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Shifts</h1>
                <p class="text-sm text-surface-bright mt-1">Manage shift schedules and assignments</p>
            </div>
            <a href="{{ route('shifts.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Add Shift
            </a>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Shift Name</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Time</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Personnel</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Created By</th>
                        <th class="text-right px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($shifts as $shift)
                        <tr class="hover:bg-surface-variant/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-on-surface">{{ $shift->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">
                                {{ $shift->start_time }} - {{ $shift->end_time }}
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">
                                {{ $shift->personnel->count() }} members
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">
                                {{ $shift->createdBy?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('shifts.show', $shift) }}" class="text-primary hover:text-primary/80 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant text-sm">No shifts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $shifts->withQueryString()->links('components.pagination') }}
    </div>
</x-layouts.app>
