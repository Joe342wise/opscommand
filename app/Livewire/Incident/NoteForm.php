<?php

namespace App\Livewire\Incident;

use App\Models\InvestigationNote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NoteForm extends Component
{
    public int $incidentId;
    public string $note = '';

    public function mount(int $incidentId): void
    {
        $this->incidentId = $incidentId;
    }

    public function addNote(): void
    {
        $this->validate([
            'note' => 'required|string|max:5000',
        ]);

        InvestigationNote::create([
            'incident_id' => $this->incidentId,
            'created_by' => Auth::id(),
            'note' => $this->note,
        ]);

        $this->note = '';
        $this->dispatch('noteAdded');
    }

    public function render()
    {
        return view('livewire.incident.note-form');
    }
}
