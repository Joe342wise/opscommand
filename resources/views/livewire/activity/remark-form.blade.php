<div>
    <form wire:submit="addRemark" class="space-y-3">
        <div>
            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[11px]">Add Remark</label>
            <textarea wire:model="remark" rows="3" class="w-full bg-slate-900 border-outline-variant text-on-surface text-body-md rounded mt-1 focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Enter your remark..."></textarea>
            @error('remark') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="bg-primary text-on-primary text-body-sm font-bold py-1.5 px-4 rounded shadow-lg shadow-primary/10 hover:opacity-90 active:scale-[0.98]">
            Add Remark
        </button>
    </form>
</div>
