<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Handover;
use App\Models\HandoverAcknowledgement;
use App\Models\HandoverItem;
use App\Models\Incident;
use App\Models\Shift;
use Illuminate\Http\Request;

class HandoverController extends Controller
{
    public function index(Request $request)
    {
        $query = Handover::with(['shift', 'createdBy', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $handovers = $query->latest()->paginate(15);
        $shifts = Shift::orderBy('name')->get();

        return view('handovers.index', compact('handovers', 'shifts'));
    }

    public function create()
    {
        $shifts = Shift::orderBy('name')->get();
        $activities = Activity::whereIn('status', ['in_progress', 'pending'])->orderBy('title')->get();
        $incidents = Incident::whereIn('status', ['open', 'investigating'])->orderBy('title')->get();
        $escalations = Escalation::where('status', 'pending')->latest()->get();

        return view('handovers.create', compact('shifts', 'activities', 'incidents', 'escalations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'summary' => 'required|string',
            'risk_summary' => 'nullable|string',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:activities,id',
            'incidents' => 'nullable|array',
            'incidents.*' => 'exists:incidents,id',
            'escalations' => 'nullable|array',
            'escalations.*' => 'exists:escalations,id',
            'item_descriptions' => 'nullable|array',
            'item_descriptions.*' => 'string',
            'item_priorities' => 'nullable|array',
            'item_priorities.*' => 'in:low,medium,high,critical',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $handover = Handover::create($validated);

        if ($request->filled('activities')) {
            foreach ($request->activities as $activityId) {
                $description = $request->input("item_descriptions.activity_{$activityId}", '');
                $priority = $request->input("item_priorities.activity_{$activityId}", 'medium');

                HandoverItem::create([
                    'handover_id' => $handover->id,
                    'activity_id' => $activityId,
                    'item_type' => 'activity',
                    'description' => $description ?: "Activity #{$activityId}",
                    'priority' => $priority,
                ]);
            }
        }

        if ($request->filled('incidents')) {
            foreach ($request->incidents as $incidentId) {
                HandoverItem::create([
                    'handover_id' => $handover->id,
                    'incident_id' => $incidentId,
                    'item_type' => 'incident',
                    'description' => "Incident #{$incidentId}",
                    'priority' => 'high',
                ]);
            }
        }

        if ($request->filled('escalations')) {
            foreach ($request->escalations as $escalationId) {
                HandoverItem::create([
                    'handover_id' => $handover->id,
                    'escalation_id' => $escalationId,
                    'item_type' => 'escalation',
                    'description' => "Escalation #{$escalationId}",
                    'priority' => 'high',
                ]);
            }
        }

        return redirect()->route('handovers.show', $handover)
            ->with('success', 'Handover created successfully.');
    }

    public function show(Handover $handover)
    {
        $handover->load(['shift', 'createdBy', 'items.activity', 'items.incident', 'items.escalation', 'acknowledgements.acknowledgedBy']);

        return view('handovers.show', compact('handover'));
    }

    public function update(Request $request, Handover $handover)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending,acknowledged,completed',
        ]);

        $handover->update($validated);

        return redirect()->route('handovers.show', $handover)
            ->with('success', 'Handover updated successfully.');
    }

    public function acknowledge(Handover $handover)
    {
        HandoverAcknowledgement::updateOrCreate(
            ['handover_id' => $handover->id, 'acknowledged_by' => auth()->id()],
            ['status' => 'acknowledged']
        );

        $allAcknowledged = $handover->acknowledgements()->where('status', 'acknowledged')->count() > 0;
        if ($allAcknowledged) {
            $handover->update(['status' => 'acknowledged']);
        }

        return redirect()->route('handovers.show', $handover)
            ->with('success', 'Handover acknowledged successfully.');
    }
}
