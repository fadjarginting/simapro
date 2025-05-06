<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class PlantController extends Controller
{
    public function index(Request $request)
    {
        // Get pagination parameters
        $maxPerPage = 50; // Set a maximum limit for perPage
        $perPage = min((int) $request->input('perPage', 10), $maxPerPage);
        $search = $request->input('search', '');
        
        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Plant Settings', 'url' => route('plants.index')],
        ];
        
        // Query to get plants
        $plantsQuery = Plant::query();
        
        // Apply search if provided
        if (!empty($search)) {
            $plantsQuery->where('name', 'like', '%' . $search . '%');
        }
        
        // Get paginated plants
        $plants = $plantsQuery->paginate($perPage)->withQueryString();
        
        return inertia('PlantSettings/IndexPlant', [
            'breadcrumbs' => $breadcrumbs,
            'plants' => $plants,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plants,name',   
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');
        
        // Check if slug already exists
        $existingSlug = Plant::where('slug', $validated['slug'])->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Plant::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }

        // Create the plant
        $plant = Plant::create($validated);

        // Return success response
        return redirect()->route('plants.index')->with('success', 'Plant created successfully');
    }

    public function update(Request $request, $plant)
    {
        // Find the plant
        $plant = Plant::findOrFail($plant);
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');
        // Check if slug already exists
        $existingSlug = Plant::where('slug', $validated['slug'])->where('id', '!=', $plant->id)->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Plant::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }
        // Update the plant
        $plant->update($validated);
        // Return success response
        return redirect()->route('plants.index')->with('success', 'Plant updated successfully');
    }

    public function destroy($id)
    {
        // Find the plant
        $plant = Plant::findOrFail($id);
        // Delete the plant
        $plant->delete();
        // Return success response
        return redirect()->route('plants.index')->with('success', 'Plant deleted successfully');
    }

    public function getPlants(): JsonResponse
    {
        // Get all plants
        $plants = Plant::all();

        return response()->json([
            'success' => true,
            'data' => $plants,
            'message' => 'Plants retrieved successfully'
        ]);
    }

    public function getPlant($id): JsonResponse
    {
        $plant = Plant::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $plant,
            'message' => 'Plant retrieved successfully'
        ]);
    }
}
