<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $personnel->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $personnel->position }} • {{ $personnel->team->name ?? 'Unassigned' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('personnel.edit', $personnel) }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Edit</a>
                <a href="{{ route('personnel.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Personnel</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Personnel Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Full Name</p>
                            <p class="text-sm text-on-surface">{{ $personnel->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Position</p>
                            <p class="text-sm text-on-surface">{{ $personnel->position }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Team</p>
                            <p class="text-sm text-on-surface">{{ $personnel->team->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Department</p>
                            <p class="text-sm text-on-surface">{{ $personnel->team?->department->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Phone</p>
                            <p class="text-sm text-on-surface">{{ $personnel->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Email</p>
                            <p class="text-sm text-on-surface">{{ $personnel->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Status</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-on-surface-variant">Status</span>
                            @if ($personnel->archived_at)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Archived</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">{{ ucfirst($personnel->status) }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-on-surface-variant">Availability</span>
                            @if ($personnel->availability === 'available')
                                <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">Available</span>
                            @elseif ($personnel->availability === 'unavailable')
                                <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Unavailable</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-warning-amber/20 text-warning-amber">On Leave</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('personnel.edit', $personnel) }}" class="block w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors text-center">Edit Personnel</a>
                        <form method="POST" action="{{ route('personnel.destroy', $personnel) }}" onsubmit="return confirm('Are you sure you want to archive this personnel record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full px-4 py-2 bg-danger-rose/10 text-danger-rose rounded-lg text-sm font-medium hover:bg-danger-rose/20 transition-colors text-center">Archive</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
