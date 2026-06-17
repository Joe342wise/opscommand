<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Personnel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $query = Personnel::with(['team.department', 'user']);

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('position', 'ilike', "%{$request->search}%");
            });
        }

        $personnel = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        $teams = Team::orderBy('name')->get();

        return view('personnel.index', compact('personnel', 'teams'));
    }

    public function create()
    {
        $teams = Team::orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('personnel.create', compact('teams', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'availability' => 'required|in:available,unavailable,on_leave',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['created_by'] = auth()->id();

        $personnel = Personnel::create($validated);

        return redirect()->route('personnel.show', $personnel)
            ->with('success', 'Personnel created successfully.');
    }

    public function show(Personnel $personnel)
    {
        $personnel->load(['team.department', 'user', 'shifts']);

        return view('personnel.show', compact('personnel'));
    }

    public function edit(Personnel $personnel)
    {
        $teams = Team::orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('personnel.edit', compact('personnel', 'teams', 'users'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'availability' => 'required|in:available,unavailable,on_leave',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['updated_by'] = auth()->id();
        $personnel->update($validated);

        return redirect()->route('personnel.show', $personnel)
            ->with('success', 'Personnel updated successfully.');
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->update(['archived_at' => now()]);

        return redirect()->route('personnel.index')
            ->with('success', 'Personnel archived successfully.');
    }
}
