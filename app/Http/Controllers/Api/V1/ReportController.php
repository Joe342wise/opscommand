<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReportRequest;
use App\Http\Resources\Api\V1\KpiSnapshotResource;
use App\Http\Resources\Api\V1\ReportResource;
use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Incident;
use App\Models\KpiSnapshot;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_reports'), 403);

        $query = Report::with(['createdBy', 'exports']);

        foreach (['type', 'status'] as $filter) {
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
                $builder->where('title', $operator, $search)
                    ->orWhere('description', $operator, $search);
            });
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'title', 'type', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $reports = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ReportResource::collection($reports)->resolve(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ]);
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->generateReportData($validated['type'], $validated['date_from'], $validated['date_to']);

        $report = Report::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'parameters' => [
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
            ],
            'data' => $data,
            'status' => 'generated',
            'created_by' => $request->user()->id,
        ])->load('createdBy');

        return $this->success([
            'report' => ReportResource::make($report),
        ], 'Report generated successfully.', 201);
    }

    public function show(Request $request, Report $report): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_reports'), 403);

        $report->load(['createdBy', 'exports']);

        return $this->success([
            'report' => ReportResource::make($report),
        ]);
    }

    public function destroy(Request $request, Report $report): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_reports'), 403);

        $report->delete();

        return $this->success(message: 'Report archived successfully.');
    }

    public function kpis(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_reports'), 403);

        $query = KpiSnapshot::query();

        if ($request->filled('kpi_name')) {
            $query->where('kpi_name', $request->input('kpi_name'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('snapshot_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('snapshot_date', '<=', $request->input('date_to'));
        }

        $sort = in_array($request->input('sort'), ['snapshot_date', 'kpi_name', 'created_at'], true)
            ? $request->input('sort')
            : 'snapshot_date';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);

        $kpis = $query->orderBy($sort, $direction)->paginate($perPage);

        $latestDate = KpiSnapshot::max('snapshot_date');
        $latestKpis = $latestDate
            ? KpiSnapshot::where('snapshot_date', $latestDate)
                ->get()
                ->pluck('value', 'kpi_name')
                ->toArray()
            : [];

        return response()->json([
            'success' => true,
            'data' => KpiSnapshotResource::collection($kpis)->resolve(),
            'meta' => [
                'current_page' => $kpis->currentPage(),
                'per_page' => $kpis->perPage(),
                'total' => $kpis->total(),
            ],
            'latest_kpis' => $latestKpis,
        ]);
    }

    private function generateReportData(string $type, string $dateFrom, string $dateTo): array
    {
        $startDate = $dateFrom;
        $endDate = $dateTo.' 23:59:59';

        return match ($type) {
            'activity' => [
                'total' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                'pending' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                'in_progress' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'in_progress')->count(),
                'by_priority' => Activity::whereBetween('created_at', [$startDate, $endDate])
                    ->select('priority', DB::raw('count(*) as total'))
                    ->groupBy('priority')
                    ->pluck('total', 'priority')
                    ->toArray(),
            ],
            'incident' => [
                'total' => Incident::whereBetween('created_at', [$startDate, $endDate])->count(),
                'open' => Incident::whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['open', 'investigating'])->count(),
                'resolved' => Incident::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
                'by_severity' => Incident::whereBetween('created_at', [$startDate, $endDate])
                    ->select('severity', DB::raw('count(*) as total'))
                    ->groupBy('severity')
                    ->pluck('total', 'severity')
                    ->toArray(),
            ],
            'escalation' => [
                'total' => Escalation::whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending' => Escalation::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                'resolved' => Escalation::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
                'by_priority' => Escalation::whereBetween('created_at', [$startDate, $endDate])
                    ->select('priority', DB::raw('count(*) as total'))
                    ->groupBy('priority')
                    ->pluck('total', 'priority')
                    ->toArray(),
            ],
            'summary' => [
                'activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                'incidents' => Incident::whereBetween('created_at', [$startDate, $endDate])->count(),
                'escalations' => Escalation::whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            default => [],
        };
    }
}
