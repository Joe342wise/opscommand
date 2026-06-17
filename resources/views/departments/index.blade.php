<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Departments</h1>
                <p class="text-sm text-surface-bright mt-1">Manage organizational departments</p>
            </div>
            <a href="{{ route('departments.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Add Department
            </a>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-4">
            @forelse ($departments as $dept)
                <a href="{{ route('departments.show', $dept) }}" class="bg-surface-container-low rounded-xl border border-outline-variant p-6 hover:border-primary/50 transition-colors">
                    <h3 class="text-sm font-semibold text-on-surface mb-1">{{ $dept->name }}</h3>
                    <p class="text-xs text-on-surface-variant mb-3">{{ $dept->description ?? 'No description' }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-on-surface-variant">{{ $dept->teams_count }} {{ Str::plural('team', $dept->teams_count) }}</span>
                        @if ($dept->archived_at)
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-danger-rose/20 text-danger-rose">Archived</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center py-12 text-on-surface-variant text-sm">No departments found.</div>
            @endforelse
        </div>

        {{ $departments->withQueryString()->links('components.pagination') }}
    </div>
</x-layouts.app>
