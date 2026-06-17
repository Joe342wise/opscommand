<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Create User</h1>
                <p class="text-sm text-surface-bright mt-1">Add a new user to the system</p>
            </div>
            <a href="{{ route('users.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Users</a>
        </div>

        <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6 max-w-2xl">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
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
                    <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-danger-rose @enderror" />
                    @error('email')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('password') border-danger-rose @enderror" />
                        @error('password')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Role</label>
                        <select name="role_id" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('role_id') border-danger-rose @enderror">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Status</label>
                        <select name="status" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('status') border-danger-rose @enderror">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-on-surface-variant hover:text-on-surface rounded-lg text-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Create User</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
