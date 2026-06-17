<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Add Personnel</h1>
                <p class="text-sm text-surface-bright mt-1">Add a new team member</p>
            </div>
            <a href="{{ route('personnel.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Personnel</a>
        </div>

        <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6 max-w-2xl">
            <form method="POST" action="{{ route('personnel.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-danger-rose @enderror" />
                    @error('name')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Position</label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('position') border-danger-rose @enderror" />
                    @error('position')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Team</label>
                        <select name="team_id" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('team_id') border-danger-rose @enderror">
                            <option value="">Select Team</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('team_id')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Linked User (optional)</label>
                        <select name="user_id"
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('user_id') border-danger-rose @enderror">
                            <option value="">None</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Status</label>
                        <select name="status" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Availability</label>
                        <select name="availability" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="available" {{ old('availability') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="unavailable" {{ old('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            <option value="on_leave" {{ old('availability') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('personnel.index') }}" class="px-4 py-2 text-on-surface-variant hover:text-on-surface rounded-lg text-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Add Personnel</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
