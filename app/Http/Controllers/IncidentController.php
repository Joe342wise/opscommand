<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentUpdate;
use App\Models\InvestigationNote;
use App\Models\ResolutionRecord;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['owner', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', "%{$request->search}%")
                  ->orWhere('description', 'ilike', "%{$request->search}%");
            });
        }

        $incidents = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        return view('incidents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|in:P1,P2,P3,P4',
            'priority' => 'required|in:low,medium,high,critical',
            'owner_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        $incident = Incident::create($validated);

        NotificationService::notifyIncidentCreated($incident->id, $incident->title, $incident->owner_id);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident created successfully.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['owner', 'createdBy', 'service', 'activities', 'updates.updatedBy', 'investigationNotes.createdBy', 'resolutionRecord.resolvedBy']);

        return view('incidents.show', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|in:P1,P2,P3,P4',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,investigating,resolved,closed',
            'owner_id' => 'required|exists:users,id',
        ]);

        if ($validated['status'] !== $incident->status) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'updated_by' => auth()->id(),
                'previous_status' => $incident->status,
                'new_status' => $validated['status'],
                'summary' => $request->get('update_summary'),
            ]);

            if ($validated['status'] === 'resolved' && ! $incident->resolved_at) {
                $validated['resolved_at'] = now();
            }

            NotificationService::notifyUsers(
                [$incident->owner_id],
                'Incident Status Updated',
                "Incident '{$incident->title}' status changed to {$validated['status']}.",
                in_array($validated['status'], ['resolved', 'closed']) ? 'info' : 'warning',
                'Incident',
                $incident->id
            );
        }

        $validated['updated_by'] = auth()->id();
        $incident->update($validated);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident updated successfully.');
    }

    public function addNote(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        InvestigationNote::create([
            'incident_id' => $incident->id,
            'created_by' => auth()->id(),
            'note' => $validated['note'],
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Investigation note added.');
    }

    public function resolve(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'summary' => 'required|string',
            'root_cause' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'preventive_action' => 'nullable|string',
        ]);

        ResolutionRecord::create([
            'incident_id' => $incident->id,
            'resolved_by' => auth()->id(),
            ...$validated,
        ]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'updated_by' => auth()->id(),
            'previous_status' => $incident->status,
            'new_status' => 'resolved',
            'summary' => $validated['summary'],
        ]);

        $incident->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident resolved successfully.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }
}
