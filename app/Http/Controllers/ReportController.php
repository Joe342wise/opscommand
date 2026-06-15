<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Escalation;
use App\Models\Incident;
use App\Models\KpiSnapshot;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('createdBy');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reports = $query->latest()->paginate(15);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:activity,incident,escalation,summary',
            'description' => 'nullable|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

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
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report generated successfully.');
    }

    public function show(Report $report)
    {
        $report->load('createdBy', 'exports');

        return view('reports.show', compact('report'));
    }

    public function kpis()
    {
        $kpis = KpiSnapshot::orderBy('snapshot_date', 'desc')
            ->orderBy('kpi_name')
            ->paginate(50);

        $latestKpis = KpiSnapshot::where('snapshot_date', KpiSnapshot::max('snapshot_date'))
            ->get()
            ->pluck('value', 'kpi_name');

        return view('reports.kpis', compact('kpis', 'latestKpis'));
    }

    private function generateReportData(string $type, string $dateFrom, string $dateTo): array
    {
        $startDate = $dateFrom;
        $endDate = $dateTo . ' 23:59:59';

        switch ($type) {
            case 'activity':
                return [
                    'total' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'completed' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                    'pending' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                    'in_progress' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'in_progress')->count(),
                    'by_priority' => Activity::whereBetween('created_at', [$startDate, $endDate])
                        ->select('priority', DB::raw('count(*) as total'))
                        ->groupBy('priority')
                        ->pluck('total', 'priority')
                        ->toArray(),
                ];
            case 'incident':
                return [
                    'total' => Incident::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'open' => Incident::whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['open', 'investigating'])->count(),
                    'resolved' => Incident::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
                    'by_severity' => Incident::whereBetween('created_at', [$startDate, $endDate])
                        ->select('severity', DB::raw('count(*) as total'))
                        ->groupBy('severity')
                        ->pluck('total', 'severity')
                        ->toArray(),
                ];
            case 'escalation':
                return [
                    'total' => Escalation::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'pending' => Escalation::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                    'resolved' => Escalation::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
                    'by_priority' => Escalation::whereBetween('created_at', [$startDate, $endDate])
                        ->select('priority', DB::raw('count(*) as total'))
                        ->groupBy('priority')
                        ->pluck('total', 'priority')
                        ->toArray(),
                ];
            case 'summary':
                return [
                    'activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'incidents' => Incident::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'escalations' => Escalation::whereBetween('created_at', [$startDate, $endDate])->count(),
                ];
            default:
                return [];
        }
    }
}
