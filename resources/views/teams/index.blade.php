<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Teams</h1>
                <p class="text-sm text-surface-bright mt-1">Manage teams across departments</p>
            </div>
            <a href="{{ route('teams.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">group_add</span>
                Add Team
            </a>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Team</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Department</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Members</th>
                        <th class="text-right px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($teams as $team)
                        <tr class="hover:bg-surface-variant/30 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ $team->name }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ Str::limit($team->description, 50) ?? 'No description' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $team->department->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $team->personnel_count ?? $team->personnel()->count() }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('teams.show', $team) }}" class="text-primary hover:text-primary/80 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant text-sm">No teams found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $teams->withQueryString()->links('components.pagination') }}
    </div>
</x-layouts.app>
