<header class="docked full-width top-0 sticky z-40 bg-surface border-b border-surface-variant/30 flex justify-between items-center h-14 px-6 ml-64">
    <div class="flex items-center gap-6">
        <div class="relative group">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
            <input type="text" placeholder="Global search..."
                   class="bg-surface-container-low border border-surface-variant/50 rounded-md pl-9 pr-4 py-1.5 text-body-sm focus:outline-none focus:ring-1 focus:ring-primary w-64 transition-all focus:w-80">
        </div>
        <div class="h-8 w-px bg-surface-variant mx-2"></div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-success-emerald animate-pulse"></span>
                <span class="font-label-caps text-label-caps text-success-emerald font-bold">SYSTEMS ONLINE</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('notifications.index') }}" class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors p-2 rounded-full hover:bg-surface-variant/30">notifications</a>
        <div class="h-6 w-px bg-surface-variant mx-2"></div>
        <div class="flex items-center gap-2">
            <span class="font-mono-data text-mono-data text-on-surface-variant bg-surface-container px-3 py-1 rounded border border-surface-variant/30"
                  x-data="{ time: '' }" x-init="setInterval(() => { const now = new Date(); time = 'UTC ' + now.getUTCHours().toString().padStart(2,'0') + ':' + now.getUTCMinutes().toString().padStart(2,'0') + ':' + now.getUTCSeconds().toString().padStart(2,'0'); }, 1000)" x-text="time">UTC 00:00:00</span>
        </div>
    </div>
</header>
