<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreHandoverItemRequest;
use App\Http\Requests\Api\V1\StoreHandoverRequest;
use App\Http\Requests\Api\V1\UpdateHandoverRequest;
use App\Http\Resources\Api\V1\HandoverResource;
use App\Models\Handover;
use App\Models\HandoverAcknowledgement;
use App\Models\HandoverItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HandoverController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_handovers'), 403);

        $query = Handover::with(['shift', 'createdBy', 'items', 'acknowledgements.acknowledgedBy']);

        foreach (['status', 'shift_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $handovers = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => HandoverResource::collection($handovers)->resolve(),
            'meta' => [
                'current_page' => $handovers->currentPage(),
                'per_page' => $handovers->perPage(),
                'total' => $handovers->total(),
            ],
        ]);
    }

    public function store(StoreHandoverRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $handover = Handover::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => $validated['status'] ?? 'draft',
        ]);

        foreach ($items as $item) {
            $this->createItem($handover, $item, $request->user()->id);
        }

        return $this->success([
            'handover' => HandoverResource::make($handover->fresh(['shift', 'items', 'acknowledgements.acknowledgedBy'])),
        ], 'Handover created successfully.', 201);
    }

    public function show(Request $request, Handover $handover): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_handovers'), 403);

        return $this->success([
            'handover' => HandoverResource::make($handover->load(['shift', 'items', 'acknowledgements.acknowledgedBy'])),
        ]);
    }

    public function update(UpdateHandoverRequest $request, Handover $handover): JsonResponse
    {
        $handover->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'handover' => HandoverResource::make($handover->fresh(['shift', 'items', 'acknowledgements.acknowledgedBy'])),
        ], 'Handover updated successfully.');
    }

    public function addItem(StoreHandoverItemRequest $request, Handover $handover): JsonResponse
    {
        $item = $this->createItem($handover, $request->validated(), $request->user()->id);

        return $this->success([
            'item' => [
                'id' => $item->id,
                'item_type' => $item->item_type,
                'description' => $item->description,
                'priority' => $item->priority,
            ],
        ], 'Handover item added successfully.', 201);
    }

    public function acknowledge(Request $request, Handover $handover): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_handovers'), 403);

        HandoverAcknowledgement::updateOrCreate(
            ['handover_id' => $handover->id, 'acknowledged_by' => $request->user()->id],
            [
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'status' => 'acknowledged',
            ]
        );

        $handover->update([
            'status' => 'acknowledged',
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'handover' => HandoverResource::make($handover->fresh(['shift', 'items', 'acknowledgements.acknowledgedBy'])),
        ], 'Handover acknowledged successfully.');
    }

    public function destroy(Request $request, Handover $handover): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_handovers'), 403);

        $handover->delete();

        return $this->success(message: 'Handover archived successfully.');
    }

    private function createItem(Handover $handover, array $item, int $userId): HandoverItem
    {
        $this->ensureItemReference($item);

        return HandoverItem::create([
            'handover_id' => $handover->id,
            'activity_id' => $item['item_type'] === 'activity' ? ($item['activity_id'] ?? null) : null,
            'incident_id' => $item['item_type'] === 'incident' ? ($item['incident_id'] ?? null) : null,
            'escalation_id' => $item['item_type'] === 'escalation' ? ($item['escalation_id'] ?? null) : null,
            'created_by' => $userId,
            'updated_by' => $userId,
            'item_type' => $item['item_type'],
            'description' => $item['description'],
            'priority' => $item['priority'],
        ]);
    }

    private function ensureItemReference(array $item): void
    {
        $reference = match ($item['item_type']) {
            'activity' => 'activity_id',
            'incident' => 'incident_id',
            'escalation' => 'escalation_id',
            default => null,
        };

        if ($reference && empty($item[$reference])) {
            throw ValidationException::withMessages([
                $reference => ["The {$reference} field is required when item_type is {$item['item_type']}."],
            ]);
        }
    }
}
