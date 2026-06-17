<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityRemark;
use App\Models\ActivityUpdate;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['owner', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
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

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'due_at' => 'nullable|date',
            'owner_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        $activity = Activity::create($validated);

        NotificationService::notifyActivityAssigned($activity->id, $activity->title, $activity->owner_id);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['owner', 'createdBy', 'updates.updatedBy', 'remarks.createdBy']);

        return view('activities.show', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,escalated,completed,cancelled',
            'owner_id' => 'required|exists:users,id',
            'due_at' => 'nullable|date',
        ]);

        if ($validated['status'] !== $activity->status) {
            ActivityUpdate::create([
                'activity_id' => $activity->id,
                'updated_by' => auth()->id(),
                'previous_status' => $activity->status,
                'new_status' => $validated['status'],
                'summary' => $request->get('update_summary'),
            ]);

            if ($validated['status'] === 'escalated') {
                NotificationService::notifyUsers(
                    \App\Models\User::where('status', 'active')->pluck('id')->toArray(),
                    'Activity Escalated',
                    "Activity '{$activity->title}' has been escalated.",
                    'warning',
                    'Activity',
                    $activity->id
                );
            }
        }

        if (isset($validated['owner_id']) && $validated['owner_id'] !== $activity->owner_id) {
            NotificationService::notifyActivityReassigned(
                $activity->id,
                $activity->title,
                $activity->owner_id,
                $validated['owner_id']
            );
        }

        $validated['updated_by'] = auth()->id();
        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    public function addRemark(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'remark' => 'required|string',
        ]);

        ActivityRemark::create([
            'activity_id' => $activity->id,
            'created_by' => auth()->id(),
            'remark' => $validated['remark'],
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Remark added successfully.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
