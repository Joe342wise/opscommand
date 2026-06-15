<x-layouts.app title="Report Incident - OpsCommand">
    <div class="max-w-[800px]">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-on-surface">Report Incident</h1>
            <p class="text-sm text-outline mt-0.5">Document a new operational incident</p>
        </div>

        <form method="POST" action="{{ route('incidents.store') }}" class="bg-surface-container rounded-xl border border-outline-variant p-6 space-y-4">
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
                    <label for="severity" class="block text-sm font-medium text-on-surface mb-1.5">Severity *</label>
                    <select id="severity" name="severity" required
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('severity') border-danger-rose @endif">
                        <option value="P4" {{ old('severity') === 'P4' ? 'selected' : '' }}>P4 - Low</option>
                        <option value="P3" {{ old('severity', 'P3') === 'P3' ? 'selected' : '' }}>P3 - Medium</option>
                        <option value="P2" {{ old('severity') === 'P2' ? 'selected' : '' }}>P2 - High</option>
                        <option value="P1" {{ old('severity') === 'P1' ? 'selected' : '' }}>P1 - Critical</option>
                    </select>
                    @error('severity')
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

            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('incidents.index') }}" class="px-4 py-2 text-sm text-outline hover:text-on-surface transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Report Incident
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
