<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Discipline;
use Illuminate\Http\Request;

class DisciplinesController extends Controller
{
    /**
     * Display a listing of the disciplines.
     */
    public function index(Request $request)
    {
        $query = Discipline::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $disciplines = $query->latest()->paginate(10);

        return Inertia::render('Disciplines/Index', [
            'disciplines' => $disciplines,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new discipline.
     */
    public function create()
    {
        // Logic to show the form for creating a new discipline
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:disciplines,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Discipline::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Discipline created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create discipline: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified discipline.
     */
    public function show($id)
    {
        // Logic to retrieve and return a specific discipline
    }

    /**
     * Show the form for editing the specified discipline.
     */
    public function edit($id)
    {
        // Logic to show the form for editing a specific discipline
    }

    /**
     * Update the specified discipline in storage.
     */
    public function update(Request $request, Discipline $discipline)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:disciplines,name,' . $discipline->id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $discipline->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Discipline updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update discipline: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified discipline from storage.
     */
    public function destroy(Discipline $discipline)
    {
        try {
            $discipline->delete();
            return redirect()->back()->with('success', 'Discipline deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete discipline: ' . $e->getMessage());
        }
    }

    /**
     * get all disciplines for select options.
     */
    public function getAllDisciplines()
    {
        $disciplines = Discipline::all(['id', 'name']);
        return response()->json($disciplines);
    }
}
