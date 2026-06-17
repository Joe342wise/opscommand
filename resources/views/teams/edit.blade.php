<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Edit Team</h1>
                <p class="text-sm text-surface-bright mt-1">Update {{ $team->name }}</p>
            </div>
            <a href="{{ route('teams.show', $team) }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Team</a>
        </div>

        <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6 max-w-2xl">
            <form method="POST" action="{{ route('teams.update', $team) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Team Name</label>
                    <input type="text" name="name" value="{{ old('name', $team->name) }}" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-danger-rose @enderror" />
                    @error('name')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Department</label>
                    <select name="department_id" required
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('department_id') border-danger-rose @enderror">
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $team->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Description</label>
                    <textarea name="description" rows="3" maxlength="1000"
                              class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('description') border-danger-rose @enderror">{{ old('description', $team->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('teams.show', $team) }}" class="px-4 py-2 text-on-surface-variant hover:text-on-surface rounded-lg text-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update Team</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
