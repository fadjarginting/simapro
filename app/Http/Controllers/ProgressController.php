<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all progress records from the database
        $progresses = Progress::all();

        // Pass them to the Vue component
        return Inertia::render('ProgressReport/Progress', [
            'progresses' => $progresses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Render the form for creating a new progress record
        return Inertia::render('ProgressReport/CreateProgress');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'request_category' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'pic_mekanikal' => 'nullable|string|max:255',
            'pic_sipil' => 'nullable|string|max:255',
            'pic_elinst' => 'nullable|string|max:255',
            'pic_proses' => 'nullable|string|max:255',
            'progress_mekanikal' => 'nullable|number|max:255',
            'progress_sipil' => 'nullable|number|max:255',
            'progress_elinst' => 'nullable|number|max:255',
            'progress_proses' => 'nullable|number|max:255',
            'detail_progress' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Create new progress record
        Progress::create($validated);

        // Redirect back to the main progress page with a success message
        return redirect()->route('progress.index')
            ->with('message', 'Progress created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Progress $progress)
    {
        // Show the details of a specific progress record
        return Inertia::render('ProgressShow', [
            'progress' => $progress
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Progress $progress)
    {
        return Inertia::render('ProgressEdit', [
            'progress' => $progress
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Progress $progress)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'request_category' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'pic_mekanikal' => 'nullable|string|max:255',
            'pic_sipil' => 'nullable|string|max:255',
            'pic_elinst' => 'nullable|string|max:255',
            'pic_proses' => 'nullable|string|max:255',
            'progress_mekanikal' => 'nullable|string|max:255',
            'progress_sipil' => 'nullable|string|max:255',
            'progress_elinst' => 'nullable|string|max:255',
            'progress_proses' => 'nullable|string|max:255',
            'detail_progress' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Update the progress record
        $progress->update($validated);

        // Redirect back to the main progress page with a success message
        return redirect()->route('progress.index')
            ->with('message', 'Progress updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Progress $progress)
    {
        // Delete the progress record
        $progress->delete();

        // Redirect back with a success message
        return redirect()->route('progress.index')
            ->with('message', 'Progress deleted successfully');
    }
}
