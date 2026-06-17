<?php

namespace App\Livewire\Activity;

use App\Models\ActivityRemark;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RemarkForm extends Component
{
    public int $activityId;
    public string $remark = '';

    public function mount(int $activityId): void
    {
        $this->activityId = $activityId;
    }

    public function addRemark(): void
    {
        $this->validate([
            'remark' => 'required|string|max:2000',
        ]);

        ActivityRemark::create([
            'activity_id' => $this->activityId,
            'created_by' => Auth::id(),
            'remark' => $this->remark,
        ]);

        $this->remark = '';
        $this->dispatch('remarkAdded');
    }

    public function render()
    {
        return view('livewire.activity.remark-form');
    }
}
