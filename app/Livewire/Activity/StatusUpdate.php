<?php

namespace App\Livewire\Activity;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StatusUpdate extends Component
{
    public Activity $activity;
    public string $newStatus = '';
    public string $summary = '';

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
        $this->newStatus = $activity->status;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'newStatus' => 'required|in:pending,in_progress,completed,escalated,cancelled',
            'summary' => 'nullable|string|max:1000',
        ]);

        $previousStatus = $this->activity->status;

        if ($previousStatus !== $this->newStatus) {
            ActivityUpdate::create([
                'activity_id' => $this->activity->id,
                'updated_by' => Auth::id(),
                'previous_status' => $previousStatus,
                'new_status' => $this->newStatus,
                'summary' => $this->summary ?: null,
            ]);

            $this->activity->update([
                'status' => $this->newStatus,
                'updated_by' => Auth::id(),
            ]);
        }

        $this->summary = '';
        $this->dispatch('statusUpdated');
    }

    public function render()
    {
        return view('livewire.activity.status-update');
    }
}
