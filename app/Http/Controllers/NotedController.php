<?php

namespace App\Http\Controllers;

use App\Models\Noted;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class NotedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // breadcrumbs
        $breadcrumbs = [
            ['name' => 'Noted Management', 'url' => route('noteds.index')],
        ];
        $search = $request->input('search');

        $query = Noted::query();

        if ($search) {
            $query->search($search);
        }

        $noteds = $query->paginate(10)->appends(app('request')->query());

        return Inertia::render('NotedManagement/Index', [
            'noteds' => $noteds,
            'breadcrumbs' => $breadcrumbs,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'noted_code' => 'required|numeric|max:99|unique:noteds,noted_code',
        ]);

        $validated['slug'] = Str::slug($validated['name'], '-');
        // Check if the slug already exists
        $existingSlug = Noted::where('slug', $validated['slug'])->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Noted::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] .= '-' . $i;
        }
        Noted::create($validated);

        return redirect()->route('noteds.index')
            ->with('message', 'Noted created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Noted  $noted
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Noted $noted)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'noted_code' => 'required|numeric|max:99|unique:noteds,noted_code,' . $noted->id,
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name'], '-');
        // Check if the slug already exists
        $existingSlug = Noted::where('slug', $validated['slug'])->where('id', '!=', $noted->id)->first();
        if ($existingSlug) {
            // Append a number to the slug if it already exists
            $i = 1;
            while (Noted::where('slug', $validated['slug'] . '-' . $i)->exists()) {
                $i++;
            }
            $validated['slug'] .= '-' . $i;
        }

        $noted->update($validated);

        return redirect()->route('noteds.index')
            ->with('message', 'Noted updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Noted  $noted
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Noted $noted)
    {
        $noted->delete();

        return redirect()->route('noteds.index')
            ->with('message', 'Noted deleted successfully');
    }
}
