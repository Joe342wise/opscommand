<div>
    @if($hasAcknowledged)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-success-emerald/10 text-success-emerald text-body-sm font-bold border border-success-emerald/20">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            Acknowledged
        </span>
    @else
        <form wire:submit="acknowledge">
            <button type="submit" class="bg-primary-container text-on-primary-container px-4 py-2 rounded-lg font-bold hover:opacity-90 active:scale-[0.98] transition-all">
                Acknowledge Handover
            </button>
        </form>
    @endif
</div>
