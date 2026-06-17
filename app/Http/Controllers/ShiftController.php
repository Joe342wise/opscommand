<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::with(['personnel', 'createdBy']);

        if ($request->filled('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        $shifts = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        $personnel = Personnel::where('status', 'active')->orderBy('name')->get();

        return view('shifts.create', compact('personnel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'personnel_ids' => 'nullable|array',
            'personnel_ids.*' => 'exists:personnel,id',
        ]);

        $validated['created_by'] = auth()->id();

        $personnelIds = $validated['personnel_ids'] ?? [];
        unset($validated['personnel_ids']);

        $shift = Shift::create($validated);

        if (! empty($personnelIds)) {
            foreach ($personnelIds as $personnelId) {
                $shift->personnel()->attach($personnelId, ['date' => now()->toDateString()]);
            }
        }

        return redirect()->route('shifts.show', $shift)
            ->with('success', 'Shift created successfully.');
    }

    public function show(Shift $shift)
    {
        $shift->load(['personnel', 'createdBy']);

        return view('shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        $personnel = Personnel::where('status', 'active')->orderBy('name')->get();
        $shift->load('personnel');

        return view('shifts.edit', compact('shift', 'personnel'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'personnel_ids' => 'nullable|array',
            'personnel_ids.*' => 'exists:personnel,id',
        ]);

        $validated['updated_by'] = auth()->id();

        $personnelIds = $validated['personnel_ids'] ?? [];
        unset($validated['personnel_ids']);

        $shift->update($validated);
        $shift->personnel()->sync($personnelIds);

        return redirect()->route('shifts.show', $shift)
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift)
    {
        $shift->update(['archived_at' => now()]);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift archived successfully.');
    }
}
