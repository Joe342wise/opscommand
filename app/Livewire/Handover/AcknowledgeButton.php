<?php

namespace App\Livewire\Handover;

use App\Models\Handover;
use App\Models\HandoverAcknowledgement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcknowledgeButton extends Component
{
    public Handover $handover;
    public bool $hasAcknowledged = false;

    public function mount(Handover $handover): void
    {
        $this->handover = $handover;
        $this->hasAcknowledged = HandoverAcknowledgement::where('handover_id', $handover->id)
            ->where('acknowledged_by', Auth::id())
            ->where('status', 'acknowledged')
            ->exists();
    }

    public function acknowledge(): void
    {
        HandoverAcknowledgement::updateOrCreate(
            ['handover_id' => $this->handover->id, 'acknowledged_by' => Auth::id()],
            ['status' => 'acknowledged']
        );

        if ($this->handover->status === 'pending') {
            $this->handover->update(['status' => 'acknowledged']);
        }

        $this->hasAcknowledged = true;
        $this->dispatch('handoverAcknowledged');
    }

    public function render()
    {
        return view('livewire.handover.acknowledge-button');
    }
}
