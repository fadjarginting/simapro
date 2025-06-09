<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Note::with(['creator:id,name', 'works'])
            ->withCount('works')
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('content', 'like', "%{$search}%");
        }

        $notes = $query->paginate(10)->appends($request->query());

        return Inertia::render('Notes/Index', [
            'notes' => $notes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Notes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:65535',
        ]);

        $validated['created_by'] = Auth::id();

        Note::create($validated);

        return Redirect::route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note): Response
    {
        $note->load(['creator:id,name', 'works.user:id,name']);
        
        return Inertia::render('Notes/Show', [
            'note' => $note,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note): Response
    {
        // Check if user can edit this note (only creator can edit)
        if ($note->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Notes/Edit', [
            'note' => $note,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        // Check if user can update this note (only creator can update)
        if ($note->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:65535',
        ]);

        $note->update($validated);

        return Redirect::route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        // Check if user can delete this note (only creator can delete)
        if ($note->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if note has associated works
        if ($note->works()->count() > 0) {
            return Redirect::back()
                ->with('error', 'Cannot delete this note because it has associated works.');
        }

        $note->delete();

        return Redirect::route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    /**
     * Get user's notes for API or AJAX requests
     */
    public function getUserNotes(Request $request)
    {
        $notes = Note::where('created_by', Auth::id())
            ->with(['works'])
            ->withCount('works')
            ->latest()
            ->get();

        return response()->json([
            'data' => $notes,
            'message' => 'Notes retrieved successfully',
        ]);
    }

    /**
     * Bulk delete notes
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'note_ids' => 'required|array',
            'note_ids.*' => 'exists:notes,id',
        ]);

        $notes = Note::whereIn('id', $validated['note_ids'])
            ->where('created_by', Auth::id())
            ->get();

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($notes as $note) {
            // Check if note has associated works
            if ($note->works()->count() > 0) {
                $skippedCount++;
                continue;
            }

            $note->delete();
            $deletedCount++;
        }

        $message = "Successfully deleted {$deletedCount} note(s).";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} note(s) were skipped because they have associated works.";
        }

        return Redirect::route('notes.index')
            ->with('success', $message);
    }

    /**
     * Search notes with advanced filters
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'created_by' => 'nullable|exists:users,id',
            'has_works' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        $query = Note::with(['creator:id,name'])
            ->withCount('works');

        // Text search
        if (!empty($validated['query'])) {
            $query->where('content', 'like', "%{$validated['query']}%");
        }

        // Date range filter
        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        // Creator filter
        if (!empty($validated['created_by'])) {
            $query->where('created_by', $validated['created_by']);
        }

        // Has works filter
        if (isset($validated['has_works'])) {
            if ($validated['has_works']) {
                $query->has('works');
            } else {
                $query->doesntHave('works');
            }
        }

        $perPage = $validated['per_page'] ?? 10;
        $notes = $query->latest()->paginate($perPage)->appends($request->query());

        return Inertia::render('Notes/Index', [
            'notes' => $notes,
            'filters' => $request->only([
                'query', 'date_from', 'date_to', 'created_by', 'has_works'
            ]),
        ]);
    }

    // /**
    //  * Get notes statistics
    //  */
    // public function getStats()
    // {
    //     $userId = Auth::id();
        
    //     $stats = [
    //         'total_notes' => Note::where('created_by', $userId)->count(),
    //         'notes_with_works' => Note::where('created_by', $userId)->has('works')->count(),
    //         'notes_without_works' => Note::where('created_by', $userId)->doesntHave('works')->count(),
    //         'total_works_from_notes' => Note::where('created_by', $userId)->withCount('works')->get()->sum('works_count'),
    //         'recent_notes' => Note::where('created_by', $userId)
    //             ->whereDate('created_at', '>=', now()->subDays(7))
    //             ->count(),
    //     ];

    //     return response()->json([
    //         'data' => $stats,
    //         'message' => 'Statistics retrieved successfully',
    //     ]);
    // }

    // /**
    //  * Duplicate a note
    //  */
    // public function duplicate(Note $note)
    // {
    //     // Check if user can access this note
    //     if ($note->created_by !== Auth::id()) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $duplicatedNote = Note::create([
    //         'content' => $note->content . ' (Copy)',
    //         'created_by' => Auth::id(),
    //     ]);

    //     return Redirect::route('notes.index')
    //         ->with('success', 'Note duplicated successfully.');
    // }

    // /**
    //  * Archive/Unarchive note (if you want to add archive functionality)
    //  */
    // public function toggleArchive(Note $note)
    // {
    //     // Check if user can modify this note
    //     if ($note->created_by !== Auth::id()) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     // This assumes you have an 'archived_at' column in your notes table
    //     if ($note->archived_at) {
    //         $note->update(['archived_at' => null]);
    //         $message = 'Note unarchived successfully.';
    //     } else {
    //         $note->update(['archived_at' => now()]);
    //         $message = 'Note archived successfully.';
    //     }

    //     return Redirect::back()->with('success', $message);
    // }
}