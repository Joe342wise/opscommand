<div>
    <form wire:submit="updateStatus" class="space-y-3">
        <div>
            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[11px]">Status</label>
            <select wire:model="newStatus" class="w-full bg-slate-900 border-outline-variant text-on-surface text-body-md rounded mt-1 focus:ring-1 focus:ring-primary focus:border-primary">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="escalated">Escalated</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div>
            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-[11px]">Summary (optional)</label>
            <textarea wire:model="summary" rows="2" class="w-full bg-slate-900 border-outline-variant text-on-surface text-body-md rounded mt-1 focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Add a summary of this status change..."></textarea>
        </div>
        <button type="submit" class="bg-primary text-on-primary text-body-sm font-bold py-1.5 px-4 rounded shadow-lg shadow-primary/10 hover:opacity-90 active:scale-[0.98]">
            Update Status
        </button>
    </form>
</div>
