<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::all();
        $categories_table = view('settings.categories.table', compact('categories'))->render();
        return view('settings.categories.index', compact('categories_table')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validate['created_by'] = auth()->user()->id;
        $validate['updated_by'] = auth()->user()->id;

        Categories::create($validate);

        $categories = Categories::all();
        $categories_table = view('settings.categories.table', compact('categories'))->render();
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
        return view('settings.categories.form', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validate['updated_by'] = auth()->user()->id;

        Categories::where('id', $id)->update($validate);

        $categories = Categories::all();
        $categories_table = view('settings.categories.table', compact('categories'))->render();
        return $categories_table;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Categories::destroy($id);

        $categories = Categories::all();
        $categories_table = view('settings.categories.table', compact('categories'))->render();
        return $categories_table;
    }
}
