<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">{{ $user->name }}</h1>
                <p class="text-sm text-surface-bright mt-1">{{ $user->email }} • {{ $user->role->name ?? 'Unassigned' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Edit User</a>
                <a href="{{ route('users.index') }}" class="text-on-surface-variant hover:text-on-surface text-sm">Back to Users</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->has('error'))
            <div class="bg-danger-rose/10 border border-danger-rose/30 rounded-lg p-4 text-danger-rose text-sm">{{ $errors->first('error') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">User Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Full Name</p>
                            <p class="text-sm text-on-surface">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Email</p>
                            <p class="text-sm text-on-surface">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Role</p>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-primary-container/20 text-primary">{{ $user->role->name ?? 'Unassigned' }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Status</p>
                            @if ($user->archived_at)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Archived</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">{{ ucfirst($user->status) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">MFA Enabled</p>
                            <p class="text-sm text-on-surface">{{ $user->mfa_enabled ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant mb-1">Last Login</p>
                            <p class="text-sm text-on-surface">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('users.edit', $user) }}" class="block w-full px-4 py-2 bg-surface-container-high text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors text-center">Edit User</a>
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to archive this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full px-4 py-2 bg-danger-rose/10 text-danger-rose rounded-lg text-sm font-medium hover:bg-danger-rose/20 transition-colors text-center">Archive User</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Account Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Created</span>
                            <span class="text-on-surface">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">User ID</span>
                            <span class="text-on-surface font-mono text-xs">#{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
