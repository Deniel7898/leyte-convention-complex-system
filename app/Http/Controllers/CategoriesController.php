<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return view('reference.categories.index', compact('categories_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reference.categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Category::create($validated);

        $categories = Category::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return $categories_table;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('reference.categories.form', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        Category::where('id', $id)->update($validated);

        $categories = Category::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return $categories_table;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $categories = Category::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return $categories_table;
    }
}