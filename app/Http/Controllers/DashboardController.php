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
            'active_incidents' => Incident::whereIn('status', ['open', 'investigating'])->count(),
            'total_incidents' => Incident::count(),
            'open_escalations' => Escalation::where('status', 'pending')->count(),
            'total_escalations' => Escalation::count(),
            'pending_handovers' => Handover::where('status', 'pending')->count(),
            'total_personnel' => DB::table('personnel')->where('status', 'active')->count(),
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

        return view('dashboard.index', compact(
            'stats',
            'recentActivities',
            'recentIncidents',
            'activitiesByPriority',
            'incidentsBySeverity',
            'escalationsByStatus'
        ));
    }
}
