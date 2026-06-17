<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $shift->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $shift->start_time }} - {{ $shift->end_time }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shifts.edit', $shift) }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Edit</a>
                <a href="{{ route('shifts.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Shifts</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Shift Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Shift Name</p>
                            <p class="text-sm text-on-surface">{{ $shift->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Schedule</p>
                            <p class="text-sm text-on-surface">{{ $shift->start_time }} - {{ $shift->end_time }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Created By</p>
                            <p class="text-sm text-on-surface">{{ $shift->createdBy?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Personnel Count</p>
                            <p class="text-sm text-on-surface">{{ $shift->personnel->count() }} members</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Assigned Personnel</h2>
                    @if ($shift->personnel->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($shift->personnel as $person)
                                <div class="flex items-center justify-between px-4 py-3 bg-surface-container rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-semibold text-xs">
                                            {{ strtoupper(substr($person->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">{{ $person->name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $person->position }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-on-surface-variant">{{ $person->pivot->date }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-on-surface-variant text-center py-4">No personnel assigned to this shift.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('shifts.edit', $shift) }}" class="block w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors text-center">Edit Shift</a>
                        <form method="POST" action="{{ route('shifts.destroy', $shift) }}" onsubmit="return confirm('Are you sure you want to archive this shift?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full px-4 py-2 bg-danger-rose/10 text-danger-rose rounded-lg text-sm font-medium hover:bg-danger-rose/20 transition-colors text-center">Archive Shift</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
