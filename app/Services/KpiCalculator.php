<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Incident;
use App\Models\Escalation;
use App\Models\KpiSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiCalculator
{
    public static function calculateAndSnapshot(?string $date = null): KpiSnapshot
    {
        $date = $date ?? now()->toDateString();
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        $kpis = [
            'total_activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
            'activity_completion_rate' => self::calculateRate(
                Activity::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                Activity::whereBetween('created_at', [$startDate, $endDate])->count()
            ),
            'total_incidents' => Incident::whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolved_incidents' => Incident::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
            'incident_resolution_rate' => self::calculateRate(
                Incident::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
                Incident::whereBetween('created_at', [$startDate, $endDate])->count()
            ),
            'critical_incidents' => Incident::whereBetween('created_at', [$startDate, $endDate])->where('severity', 'P1')->count(),
            'total_escalations' => Escalation::whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolved_escalations' => Escalation::whereBetween('created_at', [$startDate, $endDate])->where('status', 'resolved')->count(),
            'avg_resolution_time_hours' => self::avgResolutionTime($startDate, $endDate),
        ];

        foreach ($kpis as $name => $value) {
            KpiSnapshot::create([
                'kpi_name' => $name,
                'value' => $value,
                'snapshot_date' => $date,
            ]);
        }

        return KpiSnapshot::where('snapshot_date', $date)->first();
    }

    public static function getLatestKpis(): array
    {
        $latestDate = KpiSnapshot::max('snapshot_date');

        if (! $latestDate) {
            return [];
        }

        return KpiSnapshot::where('snapshot_date', $latestDate)
            ->pluck('value', 'kpi_name')
            ->toArray();
    }

    private static function calculateRate(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    private static function avgResolutionTime(Carbon $startDate, Carbon $endDate): float
    {
        $resolvedIncidents = Incident::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedIncidents->isEmpty()) {
            return 0.0;
        }

        $totalHours = $resolvedIncidents->sum(function ($incident) {
            return $incident->created_at->diffInHours($incident->resolved_at);
        });

        return round($totalHours / $resolvedIncidents->count(), 2);
    }
}
