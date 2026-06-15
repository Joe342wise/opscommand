<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Add Service</h1>
                <p class="text-sm text-surface-bright mt-1">Register a new service for monitoring</p>
            </div>
        </div>

        <form method="POST" action="{{ route('services.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                <h2 class="text-lg font-medium text-on-surface mb-4">Service Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Service name">
                        @error('name')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Category *</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="e.g., Infrastructure, Application">
                        @error('category')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-bright mb-1">Status *</label>
                        <select name="status" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary">
                            <option value="healthy" {{ old('status') === 'healthy' ? 'selected' : '' }}>Healthy</option>
                            <option value="warning" {{ old('status') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="critical" {{ old('status') === 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('status')
                            <p class="text-danger-rose text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-surface-bright mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-surface-container-high border border-outline-variant rounded-lg px-3 py-2 text-on-surface text-sm focus:outline-none focus:border-primary" placeholder="Describe the service...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('services.index') }}" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:opacity-90">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-medium hover:opacity-90">Create Service</button>
            </div>
        </form>
    </div>
</x-layouts.app>
