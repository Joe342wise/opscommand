<aside class="fixed left-0 top-0 h-screen w-64 bg-surface-container-low border-r border-outline-variant flex flex-col z-40">
    <div class="p-5 border-b border-outline-variant">
        <h1 class="text-lg font-semibold text-primary tracking-tight">OpsCommand</h1>
        <p class="text-xs text-outline mt-0.5">Operations Management</p>
    </div>
    <nav class="flex-1 p-3 space-y-1">
        @php
            $navItems = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'activities.index', 'label' => 'Activities', 'icon' => 'task_alt'],
                ['route' => 'incidents.index', 'label' => 'Incidents', 'icon' => 'warning'],
                ['route' => 'escalations.index', 'label' => 'Escalations', 'icon' => 'trending_up'],
                ['route' => 'handovers.index', 'label' => 'Handover Board', 'icon' => 'swap_horiz'],
                ['route' => 'services.index', 'label' => 'System Health', 'icon' => 'monitor_heart'],
                ['route' => 'watch-team.index', 'label' => 'Watch Team', 'icon' => 'group'],
                ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'assessment'],
            ];
        @endphp
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                      {{ request()->routeIs($item['route']) ? 'bg-primary-container text-on-primary-container' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
    <div class="p-4 border-t border-outline-variant">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container text-sm font-semibold">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-on-surface truncate">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-xs text-outline truncate">{{ auth()->user()->role->name ?? 'Role' }}</p>
            </div>
        </div>
    </div>
</aside>
