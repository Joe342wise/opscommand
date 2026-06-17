<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEscalationRequest;
use App\Http\Requests\Api\V1\UpdateEscalationRequest;
use App\Http\Resources\Api\V1\EscalationResource;
use App\Models\Escalation;
use App\Models\EscalationHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscalationController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('escalate_incidents'), 403);

        $query = Escalation::with(['owner', 'targetTeam', 'activity', 'incident']);

        foreach (['status', 'priority', 'target_team_id', 'owner_id', 'activity_id', 'incident_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('search')) {
            $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where('reason', $operator, '%'.$request->input('search').'%');
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'priority', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $escalations = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => EscalationResource::collection($escalations)->resolve(),
            'meta' => [
                'current_page' => $escalations->currentPage(),
                'per_page' => $escalations->perPage(),
                'total' => $escalations->total(),
            ],
        ]);
    }

    public function store(StoreEscalationRequest $request): JsonResponse
    {
        $escalation = Escalation::create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ])->load(['owner', 'targetTeam', 'activity', 'incident']);

        return $this->success([
            'escalation' => EscalationResource::make($escalation),
        ], 'Escalation created successfully.', 201);
    }

    public function show(Request $request, Escalation $escalation): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('escalate_incidents'), 403);

        $escalation->load(['owner', 'targetTeam', 'activity', 'incident', 'histories.changedBy']);

        return $this->success([
            'escalation' => EscalationResource::make($escalation),
            'histories' => $escalation->histories->map(fn (EscalationHistory $history) => [
                'id' => $history->id,
                'previous_status' => $history->previous_status,
                'new_status' => $history->new_status,
                'summary' => $history->summary,
                'changed_by' => $history->changedBy ? [
                    'id' => $history->changedBy->id,
                    'name' => $history->changedBy->name,
                ] : null,
                'created_at' => $history->created_at?->toISOString(),
            ]),
        ]);
    }

    public function update(UpdateEscalationRequest $request, Escalation $escalation): JsonResponse
    {
        return $this->updateEscalation($escalation, $request->validated(), $request->user()->id);
    }

    public function close(Request $request, Escalation $escalation): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('escalate_incidents'), 403);

        $request->validate(['summary' => ['nullable', 'string']]);

        return $this->updateEscalation($escalation, [
            'status' => 'resolved',
            'update_summary' => $request->input('summary'),
        ], $request->user()->id, 'Escalation closed successfully.');
    }

    public function destroy(Request $request, Escalation $escalation): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('escalate_incidents'), 403);

        $escalation->delete();

        return $this->success(message: 'Escalation archived successfully.');
    }

    private function updateEscalation(Escalation $escalation, array $validated, int $userId, string $message = 'Escalation updated successfully.'): JsonResponse
    {
        if (array_key_exists('status', $validated) && $validated['status'] !== $escalation->status) {
            EscalationHistory::create([
                'escalation_id' => $escalation->id,
                'created_by' => $userId,
                'updated_by' => $userId,
                'changed_by' => $userId,
                'previous_status' => $escalation->status,
                'new_status' => $validated['status'],
                'summary' => $validated['update_summary'] ?? null,
            ]);
        }

        unset($validated['update_summary']);

        $escalation->update([
            ...$validated,
            'updated_by' => $userId,
        ]);

        return $this->success([
            'escalation' => EscalationResource::make($escalation->fresh(['owner', 'targetTeam', 'activity', 'incident'])),
        ], $message);
    }
}
