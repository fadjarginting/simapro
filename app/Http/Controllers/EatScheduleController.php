<?php

namespace App\Http\Controllers;

use App\Models\EatSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EatScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eatSchedules = EatSchedule::where('user_id', Auth::id())
            ->orderBy('scheduled_time', 'asc')
            ->get();
            
        return Inertia::render('EatSchedule/Index', [
            'eatSchedules' => $eatSchedules
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('EatSchedule/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_type' => 'required|string',
            'scheduled_time' => 'required|date',
            'food_items' => 'nullable|string',
            'calories' => 'nullable|integer',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean'
        ]);

        $eatSchedule = new EatSchedule();
        $eatSchedule->user_id = Auth::id();
        $eatSchedule->meal_type = $validated['meal_type'];
        $eatSchedule->scheduled_time = $validated['scheduled_time'];
        $eatSchedule->food_items = $validated['food_items'] ?? null;
        $eatSchedule->calories = $validated['calories'] ?? null;
        $eatSchedule->notes = $validated['notes'] ?? null;
        $eatSchedule->is_completed = $validated['is_completed'] ?? false;
        $eatSchedule->save();

        return redirect()->route('eat-schedules.index')
            ->with('message', 'Meal schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EatSchedule $eatSchedule)
    {
        // Ensure user can only view their own schedules
        if ($eatSchedule->user_id !== Auth::id()) {
            abort(403);
        }
        
        return Inertia::render('EatSchedule/Show', [
            'eatSchedule' => $eatSchedule
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EatSchedule $eatSchedule)
    {
        // Ensure user can only edit their own schedules
        if ($eatSchedule->user_id !== Auth::id()) {
            abort(403);
        }
        
        return Inertia::render('EatSchedule/Edit', [
            'eatSchedule' => $eatSchedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EatSchedule $eatSchedule)
    {
        // Ensure user can only update their own schedules
        if ($eatSchedule->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'meal_type' => 'required|string',
            'scheduled_time' => 'required|date',
            'food_items' => 'nullable|string',
            'calories' => 'nullable|integer',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean'
        ]);

        $eatSchedule->update($validated);

        return redirect()->route('eat-schedules.index')
            ->with('message', 'Meal schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EatSchedule $eatSchedule)
    {
        // Ensure user can only delete their own schedules
        if ($eatSchedule->user_id !== Auth::id()) {
            abort(403);
        }
        
        $eatSchedule->delete();

        return redirect()->route('eat-schedules.index')
            ->with('message', 'Meal schedule deleted successfully.');
    }
    
    /**
     * Mark a meal as completed
     */
    public function markAsCompleted(EatSchedule $eatSchedule)
    {
        // Ensure user can only update their own schedules
        if ($eatSchedule->user_id !== Auth::id()) {
            abort(403);
        }
        
        $eatSchedule->is_completed = true;
        $eatSchedule->save();
        
        return redirect()->back()
            ->with('message', 'Meal marked as completed.');
    }
}