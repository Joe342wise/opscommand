<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $team->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $team->department->name ?? 'Unassigned' }} • {{ $team->personnel->count() }} members</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('teams.edit', $team) }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Edit</a>
                <a href="{{ route('teams.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Teams</a>
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
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Team Members</h2>
                    @if ($team->personnel->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($team->personnel as $person)
                                <a href="{{ route('personnel.show', $person) }}" class="flex items-center justify-between px-4 py-3 bg-surface-container rounded-lg hover:bg-surface-variant/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-semibold text-xs">
                                            {{ strtoupper(substr($person->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">{{ $person->name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $person->position }}</p>
                                        </div>
                                    </div>
                                    @if ($person->availability === 'available')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-success-emerald/20 text-success-emerald">Available</span>
                                    @elseif ($person->availability === 'unavailable')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-danger-rose/20 text-danger-rose">Unavailable</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-warning-amber/20 text-warning-amber">On Leave</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-on-surface-variant text-center py-4">No members in this team.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('teams.edit', $team) }}" class="block w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors text-center">Edit Team</a>
                        <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Are you sure you want to archive this team?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full px-4 py-2 bg-danger-rose/10 text-danger-rose rounded-lg text-sm font-medium hover:bg-danger-rose/20 transition-colors text-center">Archive Team</button>
                        </form>
                    </div>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Department</span>
                            <span class="text-on-surface">{{ $team->department->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Members</span>
                            <span class="text-on-surface">{{ $team->personnel->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Created</span>
                            <span class="text-on-surface">{{ $team->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
