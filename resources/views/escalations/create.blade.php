<x-layouts.app title="New Escalation - OpsCommand">
    <div class="max-w-[800px]">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-on-surface">New Escalation</h1>
            <p class="text-sm text-outline mt-0.5">Escalate an activity or incident for higher-level support</p>
        </div>

        <form method="POST" action="{{ route('escalations.store') }}" class="bg-surface-container rounded-xl border border-outline-variant p-6 space-y-4">
            @csrf
            <div>
                <label for="reason" class="block text-sm font-medium text-on-surface mb-1.5">Reason for Escalation *</label>
                <textarea id="reason" name="reason" rows="3" required
                          class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('reason') border-danger-rose @endif">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
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

                <div>
                    <label for="target_team_id" class="block text-sm font-medium text-on-surface mb-1.5">Target Team *</label>
                    <select id="target_team_id" name="target_team_id" required
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('target_team_id') border-danger-rose @endif">
                        <option value="">Select team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ old('target_team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('target_team_id')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="activity_id" class="block text-sm font-medium text-on-surface mb-1.5">Linked Activity</label>
                    <select id="activity_id" name="activity_id"
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('activity_id') border-danger-rose @endif">
                        <option value="">None</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : '' }}>{{ $activity->title }}</option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="incident_id" class="block text-sm font-medium text-on-surface mb-1.5">Linked Incident</label>
                    <select id="incident_id" name="incident_id"
                            class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('incident_id') border-danger-rose @endif">
                        <option value="">None</option>
                        @foreach ($incidents as $incident)
                            <option value="{{ $incident->id }}" {{ old('incident_id') == $incident->id ? 'selected' : '' }}>{{ $incident->title }}</option>
                        @endforeach
                    </select>
                    @error('incident_id')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('escalations.index') }}" class="px-4 py-2 text-sm text-outline hover:text-on-surface transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Create Escalation
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
