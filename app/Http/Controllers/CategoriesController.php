<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Helper: Get paginated categories cards with inventory count
     */
    private function getCategoriesCards()
    {
        $categories = Category::withCount('inventories')
            ->orderBy('created_at', 'desc') // newest first
            ->get(); // get all categories without pagination

        return view('reference.categories.cards', compact('categories'))->render();
    }

    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('inventories')
            ->orderBy('created_at', 'desc') // newest first
            ->get(); // get all categories without pagination

        return view('reference.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show create form (modal)
     */
    public function create()
    {
        return view('reference.categories.form');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:consumable,non-consumable',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Category::create($validated);

        return $this->getCategoriesCards();
    }

    /**
     * Show edit form (modal)
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('reference.categories.form', compact('category'));
    }

    /**
     * Update an existing category
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:consumable,non-consumable',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        Category::where('id', $id)->update($validated);

        return $this->getCategoriesCards();
    }

    /**
     * Delete a category
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return $this->getCategoriesCards();
    }
}
