<?php

namespace App\Livewire\Search;

use App\Models\Activity;
use App\Models\Incident;
use App\Models\Personnel;
use App\Models\Service;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $showResults = false;

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;

            return;
        }

        $search = $this->query;

        $activities = Activity::where('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%")
            ->take(5)
            ->get()
            ->map(fn ($a) => ['type' => 'Activity', 'id' => $a->id, 'title' => $a->title, 'url' => route('activities.show', $a)]);

        $incidents = Incident::where('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%")
            ->take(5)
            ->get()
            ->map(fn ($i) => ['type' => 'Incident', 'id' => $i->id, 'title' => $i->title, 'url' => route('incidents.show', $i)]);

        $personnel = Personnel::where('name', 'ilike', "%{$search}%")
            ->orWhere('email', 'ilike', "%{$search}%")
            ->take(5)
            ->get()
            ->map(fn ($p) => ['type' => 'Personnel', 'id' => $p->id, 'title' => $p->name, 'url' => '#']);

        $services = Service::where('name', 'ilike', "%{$search}%")
            ->take(5)
            ->get()
            ->map(fn ($s) => ['type' => 'Service', 'id' => $s->id, 'title' => $s->name, 'url' => route('services.show', $s)]);

        $this->results = $activities->merge($incidents)->merge($personnel)->merge($services)->take(10)->toArray();
        $this->showResults = count($this->results) > 0;
    }

    public function closeResults(): void
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.search.global-search');
    }
}
