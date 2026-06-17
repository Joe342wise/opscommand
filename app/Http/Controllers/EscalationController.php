<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\EscalationHistory;
use App\Models\Team;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class EscalationController extends Controller
{
    public function index(Request $request)
    {
        $query = Escalation::with(['owner', 'targetTeam', 'activity', 'incident']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('target_team_id')) {
            $query->where('target_team_id', $request->target_team_id);
        }
        if ($request->filled('search')) {
            $query->where('reason', 'ilike', "%{$request->search}%");
        }

        $escalations = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $teams = Team::orderBy('name')->get();

        return view('escalations.index', compact('escalations', 'teams'));
    }

    public function create()
    {
        $teams = Team::orderBy('name')->get();
        $activities = \App\Models\Activity::where('status', '!=', 'completed')->orderBy('title')->get();
        $incidents = \App\Models\Incident::where('status', '!=', 'resolved')->orderBy('title')->get();

        return view('escalations.create', compact('teams', 'activities', 'incidents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'target_team_id' => 'required|exists:teams,id',
            'activity_id' => 'nullable|exists:activities,id',
            'incident_id' => 'nullable|exists:incidents,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['owner_id'] = auth()->id();
        $validated['status'] = 'pending';

        $escalation = Escalation::create($validated);

        NotificationService::notifyEscalationCreated($escalation->id, $escalation->target_team_id, $escalation->reason);

        return redirect()->route('escalations.show', $escalation)
            ->with('success', 'Escalation created successfully.');
    }

    public function show(Escalation $escalation)
    {
        $escalation->load(['owner', 'targetTeam', 'activity', 'incident', 'createdBy', 'histories.changedBy']);

        return view('escalations.show', compact('escalation'));
    }

    public function update(Request $request, Escalation $escalation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,cancelled',
        ]);

        if ($validated['status'] !== $escalation->status) {
            EscalationHistory::create([
                'escalation_id' => $escalation->id,
                'changed_by' => auth()->id(),
                'previous_status' => $escalation->status,
                'new_status' => $validated['status'],
                'summary' => $request->get('update_summary'),
            ]);
        }

        $escalation->update($validated);

        return redirect()->route('escalations.show', $escalation)
            ->with('success', 'Escalation updated successfully.');
    }

    public function destroy(Escalation $escalation)
    {
        $escalation->delete();

        return redirect()->route('escalations.index')
            ->with('success', 'Escalation deleted successfully.');
    }
}
