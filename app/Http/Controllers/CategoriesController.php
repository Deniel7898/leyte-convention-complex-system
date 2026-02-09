<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Support\Facades\Auth;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::all();
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

        Categories::create($validated);

        $categories = Categories::all();
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
        $category = Categories::findOrFail($id);
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

        $validated['created_by'] = Auth::id();

        Categories::where('id', $id)->update($validated);

        $categories = Categories::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return $categories_table;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();

        $categories = Categories::all();
        $categories_table = view('reference.categories.table', compact('categories'))->render();
        return $categories_table;
    }
}
