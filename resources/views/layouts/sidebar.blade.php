<aside class="fixed left-0 top-0 h-screen w-64 bg-surface-container-low border-r border-surface-variant flex flex-col py-margin-page gap-stack-gap z-50">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-on-primary text-xl">security</span>
        </div>
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-primary">OpsCommand</h1>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest opacity-70">NOC Incident Center</p>
        </div>
    </div>

    <nav class="flex-1 px-3 space-y-1">
        @php
            $navItems = [
                ['route' => 'dashboard.index', 'label' => 'Active Command', 'icon' => 'dashboard'],
                ['route' => 'incidents.index', 'label' => 'Incidents', 'icon' => 'emergency'],
                ['route' => 'handovers.index', 'label' => 'Handover Board', 'icon' => 'swap_horiz'],
                ['route' => 'services.index', 'label' => 'System Health', 'icon' => 'analytics'],
                ['route' => 'watch-team.index', 'label' => 'Watch Team', 'icon' => 'groups'],
            ];

            $adminItems = [
                ['route' => 'users.index', 'label' => 'User Management', 'icon' => 'manage_accounts', 'permission' => 'manage_users'],
                ['route' => 'personnel.index', 'label' => 'Personnel', 'icon' => 'badge', 'permission' => 'manage_users'],
                ['route' => 'departments.index', 'label' => 'Departments', 'icon' => 'domain', 'permission' => 'manage_users'],
                ['route' => 'teams.index', 'label' => 'Teams', 'icon' => 'groups', 'permission' => 'manage_users'],
                ['route' => 'shifts.index', 'label' => 'Shifts', 'icon' => 'schedule', 'permission' => 'manage_users'],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 transition-all rounded-sm group
                      {{ request()->routeIs($item['route']) ? 'text-primary font-bold border-r-2 border-primary bg-primary/5' : 'text-on-surface-variant font-medium hover:bg-surface-variant hover:text-on-surface' }}">
                <span class="material-symbols-outlined {{ request()->routeIs($item['route']) ? 'fill-icon' : 'group-hover:scale-110 transition-transform' }}"
                      @if(request()->routeIs($item['route'])) style="font-variation-settings: 'FILL' 1;" @endif>
                    {{ $item['icon'] }}</span>
                <span class="font-body-sm text-body-sm">{{ $item['label'] }}</span>
            </a>
        @endforeach

        @if (auth()->user()?->hasAnyPermission(['manage_users']))
            <div class="pt-2 mt-2 border-t border-surface-variant/30">
                <p class="px-3 py-1 text-[10px] font-label-caps text-on-surface-variant uppercase tracking-widest">Administration</p>
                @foreach ($adminItems as $item)
                    @if (auth()->user()?->hasPermission($item['permission']))
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 transition-all rounded-sm group
                                  {{ request()->routeIs($item['route']) ? 'text-primary font-bold border-r-2 border-primary bg-primary/5' : 'text-on-surface-variant font-medium hover:bg-surface-variant hover:text-on-surface' }}">
                            <span class="material-symbols-outlined {{ request()->routeIs($item['route']) ? 'fill-icon' : 'group-hover:scale-110 transition-transform' }}"
                                  @if(request()->routeIs($item['route'])) style="font-variation-settings: 'FILL' 1;" @endif>
                                {{ $item['icon'] }}</span>
                            <span class="font-body-sm text-body-sm">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </nav>

    <div class="mt-auto px-6 py-4 border-t border-surface-variant/30">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:bg-surface-variant/30 rounded-lg p-2 transition-colors">
            <div class="relative">
                <div class="w-10 h-10 rounded-full border border-surface-variant shadow-lg bg-primary-container flex items-center justify-center text-on-primary-container font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-success-emerald rounded-full border-2 border-surface-container-low"></div>
            </div>
            <div class="overflow-hidden">
                <p class="font-body-sm font-semibold truncate text-on-surface">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="font-label-caps text-[10px] text-on-surface-variant truncate">{{ auth()->user()->role->name ?? 'Watch Commander' }}</p>
            </div>
        </a>
    </div>
</aside>
