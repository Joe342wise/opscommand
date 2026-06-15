<x-layouts.app title="New Activity - OpsCommand">
    <div class="max-w-[800px]">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-on-surface">New Activity</h1>
            <p class="text-sm text-outline mt-0.5">Create a new operational activity</p>
        </div>

        <form method="POST" action="{{ route('activities.store') }}" class="bg-surface-container rounded-xl border border-outline-variant p-6 space-y-4">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-on-surface mb-1.5">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('title') border-danger-rose @endif">
                @error('title')
                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-on-surface mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('description') border-danger-rose @endif">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-on-surface mb-1.5">Category</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}"
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('category') border-danger-rose @endif">
                    @error('category')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-on-surface mb-1.5">Priority *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('priority') border-danger-rose @endif">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-on-surface mb-1.5">Assign To *</label>
                    <select id="owner_id" name="owner_id" required
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('owner_id') border-danger-rose @endif">
                        <option value="">Select user</option>
                        @foreach (\App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_at" class="block text-sm font-medium text-on-surface mb-1.5">Due Date</label>
                    <input type="datetime-local" id="due_at" name="due_at" value="{{ old('due_at') }}"
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('due_at') border-danger-rose @endif">
                    @error('due_at')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('activities.index') }}" class="px-4 py-2 text-sm text-outline hover:text-on-surface transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Create Activity
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
