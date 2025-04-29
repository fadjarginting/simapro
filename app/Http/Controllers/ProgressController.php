<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index()
    {
        $progresses = Progress::all();

        return Inertia::render('ProgressReport/Progress', [
            'progresses' => $progresses
        ]);
    }

    public function create()
    {
        return Inertia::render('ProgressReport/CreateProgress');
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_category' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'pic_mekanikal' => 'required|string|max:255',
            'pic_sipil' => 'required|string|max:255',
            'pic_elinst' => 'required|string|max:255',
            'pic_proses' => 'required|string|max:255',
            'progress_mekanikal' => 'required|integer|min:0|max:100',
            'progress_sipil' => 'required|integer|min:0|max:100',
            'progress_elinst' => 'required|integer|min:0|max:100',
            'progress_proses' => 'required|integer|min:0|max:100',
            'detail_progress' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        Progress::create($request->all());

        return redirect()->route('progress.index')
            ->with('success', 'Progress created successfully.');
    }
    
    public function show($id)
    {
        $progress = Progress::findOrFail($id);

        return Inertia::render('ProgressReport/ShowProgress', [
            'progress' => $progress
        ]);
    }

    public function edit($id)
    {
        $progress = Progress::findOrFail($id);

        return Inertia::render('ProgressReport/EditProgress', [
            'progress' => $progress
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'request_category' => 'required|string|max:255',
            'status_verifikasi' => 'required|string|max:255',
            'pic_mekanikal' => 'required|string|max:255',
            'pic_sipil' => 'required|string|max:255',
            'pic_elinst' => 'required|string|max:255',
            'pic_proses' => 'required|string|max:255',
            'progress_mekanikal' => 'required|integer|min:0|max:100',
            'progress_sipil' => 'required|integer|min:0|max:100',
            'progress_elinst' => 'required|integer|min:0|max:100',
            'progress_proses' => 'required|integer|min:0|max:100',
            'detail_progress' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $progress = Progress::findOrFail($id);
        $progress->update($validated);

        return redirect()->route('progress.index')
            ->with('success', 'Progress updated successfully.');
    }

    public function destroy($id)
    {
        $progress = Progress::findOrFail($id);
        $progress->delete();

        return redirect()->route('progress.index')
            ->with('success', 'Progress deleted successfully.');
    }
}
