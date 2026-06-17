<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $department->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $department->description ?? 'No description' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('departments.edit', $department) }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Edit</a>
                <a href="{{ route('departments.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Departments</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->has('error'))
            <div class="bg-danger-rose/10 border border-danger-rose/30 rounded-lg p-4 text-danger-rose text-sm">{{ $errors->first('error') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-on-surface">Teams</h2>
                        <a href="{{ route('teams.create', ['department_id' => $department->id]) }}" class="text-xs text-primary hover:text-primary/80">+ Add Team</a>
                    </div>
                    @if ($department->teams->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($department->teams as $team)
                                <a href="{{ route('teams.show', $team) }}" class="flex items-center justify-between px-4 py-3 bg-surface-container rounded-lg hover:bg-surface-variant/50 transition-colors">
                                    <div>
                                        <p class="text-sm font-medium text-on-surface">{{ $team->name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $team->description ?? 'No description' }}</p>
                                    </div>
                                    <span class="text-xs text-on-surface-variant">{{ $team->personnel_count ?? $team->personnel()->count() }} members</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-on-surface-variant text-center py-4">No teams in this department.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('departments.edit', $department) }}" class="block w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors text-center">Edit Department</a>
                        <form method="POST" action="{{ route('departments.destroy', $department) }}" onsubmit="return confirm('Are you sure you want to archive this department?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full px-4 py-2 bg-danger-rose/10 text-danger-rose rounded-lg text-sm font-medium hover:bg-danger-rose/20 transition-colors text-center">Archive Department</button>
                        </form>
                    </div>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Teams</span>
                            <span class="text-on-surface">{{ $department->teams->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Created</span>
                            <span class="text-on-surface">{{ $department->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
