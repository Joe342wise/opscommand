<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Incident;
use App\Models\Personnel;
use App\Models\Shift;
use Illuminate\Http\Request;

class WatchTeamController extends Controller
{
    public function index(Request $request)
    {
        $currentShift = Shift::where('is_active', true)->first();

        $personnel = Personnel::with(['team.department', 'shifts'])
            ->where('status', 'active')
            ->get();

        $personnelWithWorkload = $personnel->map(function ($p) {
            $ownerId = $p->user_id;

            $p->assigned_activities = Activity::where('owner_id', $ownerId)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();

            $p->active_incidents = Incident::where('owner_id', $ownerId)
                ->whereIn('status', ['open', 'investigating'])
                ->count();

            $p->owned_escalations = Escalation::where('owner_id', $ownerId)
                ->where('status', 'pending')
                ->count();

            $p->completed_activities = Activity::where('owner_id', $ownerId)
                ->where('status', 'completed')
                ->count();

            $p->total_activities = Activity::where('owner_id', $ownerId)
                ->count();

            return $p;
        });

        $teams = $personnelWithWorkload->groupBy(fn ($p) => $p->team->name ?? 'Unassigned')
            ->map(function ($members, $teamName) {
                return [
                    'name' => $teamName,
                    'members' => $members,
                    'total_activities' => $members->sum('total_activities'),
                    'completed_activities' => $members->sum('completed_activities'),
                    'active_incidents' => $members->sum('active_incidents'),
                    'pending_escalations' => $members->sum('owned_escalations'),
                ];
            });

        $stats = [
            'total_personnel' => $personnelWithWorkload->count(),
            'on_shift' => $personnel->filter(fn ($p) => $p->shifts->contains($currentShift?->id))->count(),
            'total_activities' => $personnelWithWorkload->sum('total_activities'),
            'completed_activities' => $personnelWithWorkload->sum('completed_activities'),
            'active_incidents' => $personnelWithWorkload->sum('active_incidents'),
            'pending_escalations' => $personnelWithWorkload->sum('owned_escalations'),
        ];

        $upcomingShifts = Shift::where('start_time', '>', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('watch-team.index', compact('currentShift', 'teams', 'stats', 'upcomingShifts'));
    }
}
