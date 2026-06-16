<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreActivityRemarkRequest;
use App\Http\Requests\Api\V1\StoreActivityRequest;
use App\Http\Requests\Api\V1\UpdateActivityRequest;
use App\Http\Resources\Api\V1\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityRemark;
use App\Models\ActivityUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasAnyPermission(['manage_activities', 'update_activities']), 403);

        $query = Activity::with(['owner', 'createdBy']);

        foreach (['status', 'priority', 'owner_id'] as $filter) {
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

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'priority', 'status', 'due_at'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $activities = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ActivityResource::collection($activities)->resolve(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = Activity::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ])->load(['owner', 'createdBy']);

        return $this->success([
            'activity' => ActivityResource::make($activity),
        ], 'Activity created successfully.', 201);
    }

    public function show(Request $request, Activity $activity): JsonResponse
    {
        abort_unless($request->user()?->hasAnyPermission(['manage_activities', 'update_activities']), 403);

        $activity->load(['owner', 'createdBy', 'updates.updatedBy', 'remarks.createdBy']);

        return $this->success([
            'activity' => ActivityResource::make($activity),
            'updates' => $activity->updates->map(fn (ActivityUpdate $update) => [
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
            'remarks' => $activity->remarks->map(fn (ActivityRemark $remark) => [
                'id' => $remark->id,
                'remark' => $remark->remark,
                'created_by' => $remark->createdBy ? [
                    'id' => $remark->createdBy->id,
                    'name' => $remark->createdBy->name,
                ] : null,
                'created_at' => $remark->created_at?->toISOString(),
            ]),
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('status', $validated) && $validated['status'] !== $activity->status) {
            ActivityUpdate::create([
                'activity_id' => $activity->id,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'previous_status' => $activity->status,
                'new_status' => $validated['status'],
                'summary' => $validated['update_summary'] ?? null,
            ]);
        }

        unset($validated['update_summary']);

        $activity->update([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'activity' => ActivityResource::make($activity->fresh(['owner', 'createdBy'])),
        ], 'Activity updated successfully.');
    }

    public function addRemark(StoreActivityRemarkRequest $request, Activity $activity): JsonResponse
    {
        $remark = ActivityRemark::create([
            'activity_id' => $activity->id,
            'created_by' => $request->user()->id,
            'remark' => $request->validated('remark'),
        ]);

        return $this->success([
            'remark' => [
                'id' => $remark->id,
                'remark' => $remark->remark,
                'created_at' => $remark->created_at?->toISOString(),
            ],
        ], 'Remark added successfully.', 201);
    }

    public function destroy(Request $request, Activity $activity): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_activities'), 403);

        $activity->delete();

        return $this->success(message: 'Activity archived successfully.');
    }
}
