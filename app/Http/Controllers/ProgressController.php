<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all progress records from the database
        $progresses = Progress::latest()->get();

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
            'title' => 'required|string|max:255',
            'plant' => 'required|string|max:255',
            'work_priority' => 'nullable|string|max:255',
            'job_type' => 'required|string|max:255',
            'request_category' => 'required|string|max:255',
            'no_erf' => 'required|string|max:255',
            'erf_approved_date' => 'required|date',
            'erf_clarification_date' => 'required|date',
            'erf_validated_date' => 'required|date',
            'lead_engineering' => 'required|string|max:255',
            'pic_mekanikal' => 'nullable|string|max:255',
            'progress_mekanikal' => 'nullable|numeric|between:0,100',
            'pic_sipil' => 'nullable|string|max:255',
            'progress_sipil' => 'nullable|numeric|between:0,100',
            'pic_elinst' => 'nullable|string|max:255',
            'progress_elinst' => 'nullable|numeric|between:0,100',
            'pic_proses' => 'nullable|string|max:255',
            'progress_proses' => 'nullable|numeric|between:0,100',
            'requesting_unit' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'deadline_initiating' => 'required|date',
            'deadline_executing' => 'required|date',
            'status' => 'required|string|max:255',
            'fase' => 'required|string|max:255',
            'progress_description' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'entry_date' => 'required|date',
        ]);

        // Clean percentage input values
        $numericFields = ['progress_mekanikal', 'progress_sipil', 'progress_elinst', 'progress_proses'];
        foreach ($numericFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = str_replace(',', '.', $validated[$field]);
            }
        }

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
        return Inertia::render('ProgressReport/ShowProgress', [
            'progress' => $progress
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Progress $progress)
    {
        // Render the form for editing a specific progress record
        return Inertia::render('ProgressReport/EditProgress', [
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
            'title' => 'required|string|max:255',
            'plant' => 'required|string|max:255',
            'work_priority' => 'nullable|string|max:255',
            'job_type' => 'required|string|max:255',
            'request_category' => 'required|string|max:255',
            'no_erf' => 'required|string|max:255',
            'erf_approved_date' => 'required|date',
            'erf_clarification_date' => 'required|date',
            'erf_validated_date' => 'required|date',
            'lead_engineering' => 'required|string|max:255',
            'pic_mekanikal' => 'nullable|string|max:255',
            'progress_mekanikal' => 'nullable|numeric|between:0,100',
            'pic_sipil' => 'nullable|string|max:255',
            'progress_sipil' => 'nullable|numeric|between:0,100',
            'pic_elinst' => 'nullable|string|max:255',
            'progress_elinst' => 'nullable|numeric|between:0,100',
            'pic_proses' => 'nullable|string|max:255',
            'progress_proses' => 'nullable|numeric|between:0,100',
            'requesting_unit' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'deadline_initiating' => 'required|date',
            'deadline_executing' => 'required|date',
            'status' => 'required|string|max:255',
            'fase' => 'required|string|max:255',
            'progress_description' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'entry_date' => 'required|date',
        ]);

        // Clean percentage input values
        $numericFields = ['progress_mekanikal', 'progress_sipil', 'progress_elinst', 'progress_proses'];
        foreach ($numericFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = str_replace(',', '.', $validated[$field]);
            }
        }

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
