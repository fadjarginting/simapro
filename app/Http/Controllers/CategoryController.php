<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get pagination parameters
        $maxPerPage = 50; // Set a maximum limit for perPage
        $perPage = min((int) $request->input('perPage', 10), $maxPerPage);
        $search = $request->input('search', '');

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Request Category Settings', 'url' => route('categories.index')],
        ];

        // Query to get categories
        $categoriesQuery = Category::query();

        // Apply search if provided
        if (!empty($search)) {
            $categoriesQuery->where('name', 'like', '%' . $search . '%');
        }

        // Get paginated categories
        $categories = $categoriesQuery->paginate($perPage)->appends(request()->query());

        return inertia('RequestCategory/Category', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');

        // Check if slug already exists
        $existingSlug = Category::where('slug', $validated['slug'])->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Category::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }

        // Create the category$category
        $category = Category::create($validated);

        // Return success response
        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $category)
    {
        // Find the category$category
        $category = Category::findOrFail($category);

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');
        // Check if slug already exists
        $existingSlug = Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Category::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] = $validated['slug'] . '-' . $i;
        }
        // Update the Category
        $category->update($validated);
        // Return success response
        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the category
        $category = Category::findOrFail($id);
        // Delete the category
        $category->delete();
        // Return success response
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
