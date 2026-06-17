<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('teams');

        if ($request->filled('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        $departments = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();

        $department = Department::create($validated);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['teams']);

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => "required|string|max:255|unique:departments,name,{$department->id}",
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = auth()->id();
        $department->update($validated);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->teams()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot archive department with active teams.']);
        }

        $department->update(['archived_at' => now()]);

        return redirect()->route('departments.index')
            ->with('success', 'Department archived successfully.');
    }
}
