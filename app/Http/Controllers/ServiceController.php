<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SlaRecord;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['metrics', 'slaRecords']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $services = $query->orderBy('name')->get();

        $stats = [
            'total' => Service::count(),
            'healthy' => Service::where('status', 'healthy')->count(),
            'warning' => Service::where('status', 'warning')->count(),
            'critical' => Service::where('status', 'critical')->count(),
        ];

        return view('services.index', compact('services', 'stats'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'required|in:healthy,warning,critical',
            'description' => 'nullable|string',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load(['metrics', 'slaRecords']);

        return view('services.show', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'status' => 'required|in:healthy,warning,critical',
        ]);

        $previousStatus = $service->status;
        $service->update($validated);

        if ($validated['status'] === 'critical' && $previousStatus !== 'critical') {
            NotificationService::notifyServiceCritical($service->id, $service->name);
            NotificationService::createAlert(
                'Service Critical',
                "Service '{$service->name}' status changed to CRITICAL",
                'critical',
                'Service',
                $service->id,
                auth()->id()
            );
        }

        return redirect()->route('services.show', $service)
            ->with('success', 'Service status updated successfully.');
    }

    public function addMetric(Request $request, Service $service)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string|max:255',
            'metric_value' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $service->metrics()->create($validated);

        return redirect()->route('services.show', $service)
            ->with('success', 'Metric added successfully.');
    }
}
