<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-on-surface">Users</h1>
                <p class="text-sm text-surface-bright mt-1">Manage user accounts and access</p>
            </div>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Add User
            </a>
        </div>

        @if (session('success'))
            <div class="bg-success-emerald/10 border border-success-emerald/30 rounded-lg p-4 text-success-emerald text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Role</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Last Login</th>
                        <th class="text-right px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface-variant/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-container/20 flex items-center justify-center text-primary font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-on-surface">{{ $user->name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-medium bg-primary-container/20 text-primary">{{ $user->role->name ?? 'Unassigned' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->archived_at)
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-danger-rose/20 text-danger-rose">Archived</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-success-emerald/20 text-success-emerald">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">
                                {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('users.show', $user) }}" class="text-primary hover:text-primary/80 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant text-sm">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->withQueryString()->links('components.pagination') }}
    </div>
</x-layouts.app>
