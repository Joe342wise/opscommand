<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Create Shift</h1>
                <p class="text-sm text-surface-bright mt-1">Define a new shift schedule</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Shifts</a>
        </div>

        <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6 max-w-2xl">
            <form method="POST" action="{{ route('shifts.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Shift Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Morning Shift, Night Shift"
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-danger-rose @enderror" />
                    @error('name')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" required
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('start_time') border-danger-rose @enderror" />
                        @error('start_time')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" required
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('end_time') border-danger-rose @enderror" />
                        @error('end_time')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Assigned Personnel</label>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 bg-surface-container-low border border-outline-variant rounded-lg">
                        @foreach ($personnel as $person)
                            <label class="flex items-center gap-2 px-2 py-1 rounded hover:bg-surface-variant cursor-pointer">
                                <input type="checkbox" name="personnel_ids[]" value="{{ $person->id }}"
                                       {{ in_array($person->id, old('personnel_ids', [])) ? 'checked' : '' }}
                                       class="rounded border-outline-variant text-primary focus:ring-primary" />
                                <span class="text-sm text-on-surface">{{ $person->name }}</span>
                            </label>
                        @endforeach
                        @if ($personnel->isEmpty())
                            <p class="text-sm text-on-surface-variant col-span-2 text-center py-4">No active personnel found.</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('shifts.index') }}" class="px-4 py-2 text-on-surface-variant hover:text-on-surface rounded-lg text-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Create Shift</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
