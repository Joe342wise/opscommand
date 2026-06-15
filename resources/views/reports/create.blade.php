<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Generate Report</h1>
                <p class="text-sm text-surface-bright mt-1">Create a new operational report</p>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-4">Report Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-surface-bright mb-1">Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Report title">
                        @error('title')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Type *</label>
                        <select name="type" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                            <option value="activity" {{ old('type') === 'activity' ? 'selected' : '' }}>Activity Report</option>
                            <option value="incident" {{ old('type') === 'incident' ? 'selected' : '' }}>Incident Report</option>
                            <option value="escalation" {{ old('type') === 'escalation' ? 'selected' : '' }}>Escalation Report</option>
                            <option value="summary" {{ old('type') === 'summary' ? 'selected' : '' }}>Summary Report</option>
                        </select>
                        @error('type')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Date Range *</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ old('date_from') }}" class="flex-1 bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                            <span class="text-surface-bright">to</span>
                            <input type="date" name="date_to" value="{{ old('date_to') }}" class="flex-1 bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                        </div>
                        @error('date_from')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('date_to')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-surface-bright mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Report description...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">Generate Report</button>
            </div>
        </form>
    </div>
</x-layouts.app>
