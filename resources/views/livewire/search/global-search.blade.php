<div class="relative group" wire:click.away="closeResults">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
    <input type="text" wire:model.live.debounce.300ms="query" placeholder="Global search..."
           class="bg-surface-container-low border border-surface-variant/50 rounded-md pl-9 pr-4 py-1.5 text-body-sm focus:outline-none focus:ring-1 focus:ring-primary w-64 transition-all focus:w-80">
    @if($showResults)
        <div class="absolute top-full left-0 mt-2 w-96 bg-surface-container-high border border-surface-variant rounded-lg shadow-xl z-50 max-h-80 overflow-y-auto">
            @forelse($results as $result)
                <a href="{{ $result['url'] }}" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-variant/50 border-b border-surface-variant/20 last:border-0">
                    <span class="font-label-caps text-[10px] px-1.5 py-0.5 rounded
                        @if($result['type'] === 'Activity') bg-primary/10 text-primary
                        @elseif($result['type'] === 'Incident') bg-danger-rose/10 text-danger-rose
                        @elseif($result['type'] === 'Personnel') bg-secondary/10 text-secondary
                        @else bg-warning-amber/10 text-warning-amber
                        @endif">
                        {{ $result['type'] }}
                    </span>
                    <span class="text-body-sm text-on-surface truncate">{{ $result['title'] }}</span>
                </a>
            @empty
                <div class="px-4 py-3 text-body-sm text-on-surface-variant">No results found.</div>
            @endforelse
        </div>
    @endif
</div>
