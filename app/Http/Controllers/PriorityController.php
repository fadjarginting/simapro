<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class PriorityController extends Controller
{
    public function index(Request $request)
    {
        // Get pagination parameters
        $maxPerPage = 50; // Set a maximum limit for perPage
        $perPage = min((int) $request->input('perPage', 10), $maxPerPage);
        $search = $request->input('search', '');

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Work Priority Settings', 'url' => route('priorities.index')],
        ];

        // Query to get priorities
        $prioritiesQuery = Priority::query();

        // Apply search if provided
        if (!empty($search)) {
            $prioritiesQuery->where('name', 'like', '%' . $search . '%');
        }

        // Get paginated priorities
        $priorities = $prioritiesQuery->paginate($perPage)->appends(request()->query());

        return inertia('WorkPriority/IndexPriority', [
            'breadcrumbs' => $breadcrumbs,
            'priorities' => $priorities,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:priorities,name',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');

        // Check if slug already exists
        $existingSlug = Priority::where('slug', $validated['slug'])->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Priority::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }

        // Create the priority
        $priority = Priority::create($validated);

        // Return success response
        return redirect()->route('priorities.index')->with('success', 'Priority created successfully');
    }

    public function update(Request $request, $priority)
    {
        // Find the priority
        $priority = Priority::findOrFail($priority);

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');
        // Check if slug already exists
        $existingSlug = Priority::where('slug', $validated['slug'])->where('id', '!=', $priority->id)->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Priority::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }
        // Update the Priority
        $priority->update($validated);
        // Return success response
        return redirect()->route('priorities.index')->with('success', 'Priority updated successfully');
    }

    public function destroy($id)
    {
        // Find the priority
        $priority = Priority::findOrFail($id);
        // Delete the priority
        $priority->delete();
        // Return success response
        return redirect()->route('priorities.index')->with('success', 'Priority deleted successfully');
    }
}
