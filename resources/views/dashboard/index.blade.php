<x-layouts.app title="Dashboard - OpsCommand">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-on-surface">Operations Dashboard</h1>
                <p class="text-sm text-outline mt-0.5">Real-time operational overview</p>
            </div>
            <div class="text-xs text-outline font-mono" x-data="{ date: '{{ now()->format('D, M d Y') }}' }" x-text="date"></div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Active Incidents</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">0</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Pending Activities</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">0</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Open Escalations</p>
                <p class="text-2xl font-semibold text-on-surface mt-1">0</p>
            </div>
            <div class="bg-surface-container p-4 rounded-xl border border-outline-variant">
                <p class="text-xs text-outline uppercase tracking-wide">Services Healthy</p>
                <p class="text-2xl font-semibold text-success-emerald mt-1">0/0</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2 bg-surface-container rounded-xl border border-outline-variant p-5">
                <h2 class="text-sm font-medium text-on-surface mb-4">Recent Activity</h2>
                <p class="text-sm text-outline">No recent activity.</p>
            </div>
            <div class="bg-surface-container rounded-xl border border-outline-variant p-5">
                <h2 class="text-sm font-medium text-on-surface mb-4">Service Status</h2>
                <p class="text-sm text-outline">No services monitored.</p>
            </div>
        </div>
    </div>
</x-layouts.app>
