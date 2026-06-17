<x-layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-on-surface">Profile Settings</h1>
            <p class="text-sm text-surface-bright mt-1">Manage your account settings</p>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Personal Information</h2>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-danger-rose @enderror" />
                                @error('name')
                                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-danger-rose @enderror" />
                                @error('email')
                                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update Profile</button>
                        </div>
                    </form>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Change Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('current_password') border-danger-rose @enderror" />
                            @error('current_password')
                                <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">New Password</label>
                                <input type="password" name="password" required
                                       class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary @error('password') border-danger-rose @enderror" />
                                @error('password')
                                    <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Account Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Role</span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-primary-container/20 text-primary">{{ $user->role->name ?? 'Unassigned' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Status</span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">{{ ucfirst($user->status) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">MFA Enabled</span>
                            <span class="text-on-surface">{{ $user->mfa_enabled ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Last Login</span>
                            <span class="text-on-surface">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
