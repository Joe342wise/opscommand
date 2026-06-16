<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Handover;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_activities' => Activity::count(),
            'completed_activities' => Activity::where('status', 'completed')->count(),
            'pending_activities' => Activity::where('status', 'pending')->count(),
            'in_progress_activities' => Activity::where('status', 'in_progress')->count(),
            'overdue_activities' => Activity::where('status', '!=', 'completed')
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
            'active_incidents' => Incident::whereIn('status', ['open', 'investigating'])->count(),
            'total_incidents' => Incident::count(),
            'open_escalations' => Escalation::where('status', 'pending')->count(),
            'total_escalations' => Escalation::count(),
            'pending_handovers' => Handover::where('status', 'pending')->count(),
            'total_personnel' => DB::table('personnel')->where('status', 'active')->count(),
            'online_personnel' => DB::table('personnel')->where('status', 'active')->limit(6)->get(),
        ];

        $recentActivities = Activity::with('owner')
            ->latest()
            ->limit(10)
            ->get();

        $recentIncidents = Incident::with('owner')
            ->latest()
            ->limit(10)
            ->get();

        $activitiesByPriority = Activity::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority');

        $incidentsBySeverity = Incident::select('severity', DB::raw('count(*) as total'))
            ->groupBy('severity')
            ->get()
            ->pluck('total', 'severity');

        $escalationsByStatus = Escalation::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $resolvedIncidents = Incident::where('status', 'resolved')
            ->where('updated_at', '>=', now()->subHours(24))
            ->get(['created_at', 'updated_at']);

        $mttr = $resolvedIncidents->isNotEmpty()
            ? round($resolvedIncidents->avg(fn ($incident) => $incident->created_at->diffInMinutes($incident->updated_at)), 1)
            : 0;

        $completedActivities = Activity::where('status', 'completed')->count();
        $completedOnTime = Activity::where('status', 'completed')
            ->where('updated_at', '<=', DB::raw('COALESCE(due_at, updated_at)'))
            ->count();

        $slaCompliance = $completedActivities > 0 ? round(($completedOnTime / $completedActivities) * 100, 1) : 100;

        return view('dashboard.index', compact(
            'stats',
            'recentActivities',
            'recentIncidents',
            'activitiesByPriority',
            'incidentsBySeverity',
            'escalationsByStatus',
            'mttr',
            'slaCompliance'
        ));
    }
}
