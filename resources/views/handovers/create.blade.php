<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Create Handover</h1>
                <p class="text-sm text-surface-bright mt-1">Document the shift handover</p>
            </div>
        </div>

        <form method="POST" action="{{ route('handovers.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-4">Handover Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Shift *</label>
                        <select name="shift_id" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                            <option value="">Select shift</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div></div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-surface-bright mb-1">Summary *</label>
                        <textarea name="summary" rows="4" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Describe the handover details...">{{ old('summary') }}</textarea>
                        @error('summary')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-surface-bright mb-1">Risk Summary</label>
                        <textarea name="risk_summary" rows="3" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Note any risks or concerns...">{{ old('risk_summary') }}</textarea>
                    </div>
                </div>
            </div>

            @if ($activities->count() > 0)
                <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                    <h2 class="text-lg font-medium text-on-surface mb-4">Activities to Hand Over</h2>
                    <div class="space-y-2">
                        @foreach ($activities as $activity)
                            <label class="flex items-center gap-3 p-3 bg-surface-container-low rounded-lg border border-outline-variant hover:border-primary/50 cursor-pointer">
                                <input type="checkbox" name="activities[]" value="{{ $activity->id }}" class="rounded bg-surface-container-high border-outline-variant text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-on-surface">{{ $activity->title }}</p>
                                    <p class="text-xs text-surface-bright">Priority: {{ ucfirst($activity->priority) }} • Status: {{ ucfirst(str_replace('_', ' ', $activity->status)) }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($incidents->count() > 0)
                <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                    <h2 class="text-lg font-medium text-on-surface mb-4">Incidents to Hand Over</h2>
                    <div class="space-y-2">
                        @foreach ($incidents as $incident)
                            <label class="flex items-center gap-3 p-3 bg-surface-container-low rounded-lg border border-outline-variant hover:border-primary/50 cursor-pointer">
                                <input type="checkbox" name="incidents[]" value="{{ $incident->id }}" class="rounded bg-surface-container-high border-outline-variant text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-on-surface">{{ $incident->title }}</p>
                                    <p class="text-xs text-surface-bright">Severity: {{ $incident->severity }} • Status: {{ ucfirst($incident->status) }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($escalations->count() > 0)
                <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                    <h2 class="text-lg font-medium text-on-surface mb-4">Escalations to Hand Over</h2>
                    <div class="space-y-2">
                        @foreach ($escalations as $escalation)
                            <label class="flex items-center gap-3 p-3 bg-surface-container-low rounded-lg border border-outline-variant hover:border-primary/50 cursor-pointer">
                                <input type="checkbox" name="escalations[]" value="{{ $escalation->id }}" class="rounded bg-surface-container-high border-outline-variant text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-on-surface">{{ $escalation->title }}</p>
                                    <p class="text-xs text-surface-bright">Priority: {{ ucfirst($escalation->priority) }} • Team: {{ $escalation->targetTeam->name ?? 'N/A' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('handovers.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">Create Handover</button>
            </div>
        </form>
    </div>
</x-layouts.app>
