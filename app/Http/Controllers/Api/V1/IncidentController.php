<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ResolveIncidentRequest;
use App\Http\Requests\Api\V1\StoreIncidentNoteRequest;
use App\Http\Requests\Api\V1\StoreIncidentRequest;
use App\Http\Requests\Api\V1\UpdateIncidentRequest;
use App\Http\Resources\Api\V1\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use App\Models\InvestigationNote;
use App\Models\ResolutionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_incidents'), 403);

        $query = Incident::with(['owner', 'service', 'createdBy']);

        foreach (['status', 'severity', 'priority', 'owner_id', 'service_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('assigned_to')) {
            $query->where('owner_id', $request->input('assigned_to'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $search = '%'.$request->input('search').'%';

            $query->where(function ($builder) use ($operator, $search) {
                $builder->where('title', $operator, $search)
                    ->orWhere('description', $operator, $search);
            });
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'severity', 'priority', 'status', 'resolved_at'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $incidents = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => IncidentResource::collection($incidents)->resolve(),
            'meta' => [
                'current_page' => $incidents->currentPage(),
                'per_page' => $incidents->perPage(),
                'total' => $incidents->total(),
            ],
        ]);
    }

    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $incident = Incident::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => 'open',
        ])->load(['owner', 'service', 'createdBy']);

        return $this->success([
            'incident' => IncidentResource::make($incident),
        ], 'Incident created successfully.', 201);
    }

    public function show(Request $request, Incident $incident): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_incidents'), 403);

        $incident->load(['owner', 'createdBy', 'service', 'activities', 'updates.updatedBy', 'investigationNotes.createdBy', 'resolutionRecord.resolvedBy']);

        return $this->success([
            'incident' => IncidentResource::make($incident),
            'updates' => $incident->updates->map(fn (IncidentUpdate $update) => [
                'id' => $update->id,
                'previous_status' => $update->previous_status,
                'new_status' => $update->new_status,
                'summary' => $update->summary,
                'updated_by' => $update->updatedBy ? [
                    'id' => $update->updatedBy->id,
                    'name' => $update->updatedBy->name,
                ] : null,
                'created_at' => $update->created_at?->toISOString(),
            ]),
            'notes' => $incident->investigationNotes->map(fn (InvestigationNote $note) => [
                'id' => $note->id,
                'note' => $note->note,
                'created_by' => $note->createdBy ? [
                    'id' => $note->createdBy->id,
                    'name' => $note->createdBy->name,
                ] : null,
                'created_at' => $note->created_at?->toISOString(),
            ]),
            'resolution' => $incident->resolutionRecord ? [
                'id' => $incident->resolutionRecord->id,
                'summary' => $incident->resolutionRecord->summary,
                'root_cause' => $incident->resolutionRecord->root_cause,
                'corrective_action' => $incident->resolutionRecord->corrective_action,
                'preventive_action' => $incident->resolutionRecord->preventive_action,
                'resolved_by' => $incident->resolutionRecord->resolvedBy ? [
                    'id' => $incident->resolutionRecord->resolvedBy->id,
                    'name' => $incident->resolutionRecord->resolvedBy->name,
                ] : null,
                'created_at' => $incident->resolutionRecord->created_at?->toISOString(),
            ] : null,
        ]);
    }

    public function update(UpdateIncidentRequest $request, Incident $incident): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('status', $validated) && $validated['status'] !== $incident->status) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'previous_status' => $incident->status,
                'new_status' => $validated['status'],
                'summary' => $validated['update_summary'] ?? null,
            ]);

            if ($validated['status'] === 'resolved' && ! $incident->resolved_at) {
                $validated['resolved_at'] = now();
            }
        }

        unset($validated['update_summary']);

        $incident->update([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'incident' => IncidentResource::make($incident->fresh(['owner', 'service', 'createdBy'])),
        ], 'Incident updated successfully.');
    }

    public function addNote(StoreIncidentNoteRequest $request, Incident $incident): JsonResponse
    {
        $note = InvestigationNote::create([
            'incident_id' => $incident->id,
            'created_by' => $request->user()->id,
            'note' => $request->validated('note'),
        ]);

        return $this->success([
            'note' => [
                'id' => $note->id,
                'note' => $note->note,
                'created_at' => $note->created_at?->toISOString(),
            ],
        ], 'Investigation note added successfully.', 201);
    }

    public function resolve(ResolveIncidentRequest $request, Incident $incident): JsonResponse
    {
        $validated = $request->validated();

        $resolution = ResolutionRecord::updateOrCreate(
            ['incident_id' => $incident->id],
            [
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'resolved_by' => $request->user()->id,
                ...$validated,
            ]
        );

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
            'previous_status' => $incident->status,
            'new_status' => 'resolved',
            'summary' => $validated['summary'],
        ]);

        $incident->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'incident' => IncidentResource::make($incident->fresh(['owner', 'service', 'createdBy'])),
            'resolution' => [
                'id' => $resolution->id,
                'summary' => $resolution->summary,
            ],
        ], 'Incident resolved successfully.');
    }

    public function destroy(Request $request, Incident $incident): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_incidents'), 403);

        $incident->delete();

        return $this->success(message: 'Incident archived successfully.');
    }
}
