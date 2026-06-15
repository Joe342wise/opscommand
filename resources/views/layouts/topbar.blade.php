<header class="sticky top-0 z-30 h-14 bg-surface border-b border-outline-variant flex items-center justify-between px-6">
    <div class="flex items-center gap-4 flex-1">
        <div class="relative w-full max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input type="text" placeholder="Search activities, incidents, services..."
                   class="w-full pl-9 pr-4 py-2 bg-surface-container border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary">
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 text-xs text-outline">
            <span class="material-symbols-outlined text-[16px]">circle</span>
            <span class="text-success-emerald">All Systems Operational</span>
        </div>
        <button class="relative p-2 rounded-lg hover:bg-surface-container text-on-surface">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-rose rounded-full"></span>
        </button>
        <div class="text-xs font-mono text-outline" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toUTCString().slice(17, 25), 1000)" x-text="time"></div>
    </div>
</header>
