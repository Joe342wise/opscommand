<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Personnel</h1>
                <p class="text-sm text-surface-bright mt-1">Manage team members and availability</p>
            </div>
            <a href="{{ route('personnel.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Add Personnel
            </a>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Personnel</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Team</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Position</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Availability</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($personnel as $person)
                        <tr class="hover:bg-surface-variant/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-semibold text-sm">
                                        {{ strtoupper(substr($person->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-on-surface">{{ $person->name }}</p>
                                        @if ($person->user)
                                            <p class="text-xs text-on-surface-variant">{{ $person->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface">{{ $person->team->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $person->position }}</td>
                            <td class="px-6 py-4">
                                @if ($person->availability === 'available')
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">Available</span>
                                @elseif ($person->availability === 'unavailable')
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Unavailable</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-warning-amber/20 text-warning-amber">On Leave</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($person->archived_at)
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Archived</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">{{ ucfirst($person->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('personnel.show', $person) }}" class="text-primary hover:text-primary/80 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant text-sm">No personnel found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $personnel->withQueryString()->links('components.pagination') }}
    </div>
</x-layouts.app>
