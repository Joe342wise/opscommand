<?php

namespace App\Livewire\Dashboard;

use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Handover;
use App\Models\Incident;
use App\Models\Personnel;
use Livewire\Component;

class StatsOverview extends Component
{
    public array $stats = [];
    public array $recentActivities = [];
    public array $recentIncidents = [];

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $this->stats = [
            'total_activities' => Activity::count(),
            'completed_activities' => Activity::where('status', 'completed')->count(),
            'pending_activities' => Activity::where('status', 'pending')->count(),
            'in_progress_activities' => Activity::where('status', 'in_progress')->count(),
            'overdue_activities' => Activity::where('status', '!=', 'completed')
                ->where('due_at', '<', now())
                ->whereNotNull('due_at')
                ->count(),
            'active_incidents' => Incident::whereIn('status', ['open', 'in_progress', 'investigating'])->count(),
            'total_incidents' => Incident::count(),
            'open_escalations' => Escalation::whereIn('status', ['pending', 'in_progress'])->count(),
            'total_escalations' => Escalation::count(),
            'pending_handovers' => Handover::where('status', 'pending')->count(),
            'active_personnel' => Personnel::where('status', 'active')->count(),
        ];

        $this->recentActivities = Activity::with('owner')
            ->latest()
            ->take(10)
            ->get()
            ->toArray();

        $this->recentIncidents = Incident::with('owner')
            ->latest()
            ->take(10)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.stats-overview');
    }
}
