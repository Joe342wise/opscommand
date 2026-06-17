<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::with(['department'])->withCount('personnel');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        $teams = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        $departments = Department::orderBy('name')->get();

        return view('teams.index', compact('teams', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();

        return view('teams.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department_id' => 'required|exists:departments,id',
        ]);

        $validated['created_by'] = auth()->id();

        $team = Team::create($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load(['department', 'personnel']);

        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $departments = Department::orderBy('name')->get();

        return view('teams.edit', compact('team', 'departments'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department_id' => 'required|exists:departments,id',
        ]);

        $validated['updated_by'] = auth()->id();
        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        if ($team->personnel()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot archive team with active personnel.']);
        }

        $team->update(['archived_at' => now()]);

        return redirect()->route('teams.index')
            ->with('success', 'Team archived successfully.');
    }
}
