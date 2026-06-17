<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceMetricRequest;
use App\Http\Requests\Api\V1\StoreServiceRequest;
use App\Http\Requests\Api\V1\UpdateServiceRequest;
use App\Http\Resources\Api\V1\ServiceMetricResource;
use App\Http\Resources\Api\V1\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_dashboard'), 403);

        $query = Service::with(['metrics', 'slaRecords', 'createdBy']);

        foreach (['status', 'category'] as $filter) {
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

        if ($request->filled('search')) {
            $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $search = '%'.$request->input('search').'%';

            $query->where(function ($builder) use ($operator, $search) {
                $builder->where('name', $operator, $search)
                    ->orWhere('category', $operator, $search)
                    ->orWhere('description', $operator, $search);
            });
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'name', 'status', 'category'], true)
            ? $request->input('sort')
            : 'name';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $services = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services)->resolve(),
            'meta' => [
                'current_page' => $services->currentPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ],
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_dashboard'), 403);

        return $this->success([
            'stats' => [
                'total' => Service::count(),
                'healthy' => Service::where('status', 'healthy')->count(),
                'warning' => Service::where('status', 'warning')->count(),
                'critical' => Service::where('status', 'critical')->count(),
            ],
        ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ])->load('createdBy');

        return $this->success([
            'service' => ServiceResource::make($service),
        ], 'Service created successfully.', 201);
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_dashboard'), 403);

        $service->load(['metrics', 'slaRecords', 'createdBy']);

        return $this->success([
            'service' => ServiceResource::make($service),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'service' => ServiceResource::make($service->fresh(['metrics', 'slaRecords', 'createdBy'])),
        ], 'Service updated successfully.');
    }

    public function addMetric(StoreServiceMetricRequest $request, Service $service): JsonResponse
    {
        $metric = $service->metrics()->create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return $this->success([
            'metric' => ServiceMetricResource::make($metric),
        ], 'Metric added successfully.', 201);
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_services'), 403);

        $service->delete();

        return $this->success(message: 'Service archived successfully.');
    }
}
