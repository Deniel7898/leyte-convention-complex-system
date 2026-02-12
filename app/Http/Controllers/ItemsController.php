<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Units;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all(); // 10 items per page
        $items_table = view('inventory.items.table', compact('items'))->render();

        return view('inventory.items.index', compact('items_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.items.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Set default availability to 1 if not provided
        $validated['availability'] = $validated['availability'] ?? 1;

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // Create the item
        $item = Item::create($validated);

        $items = Item::withCount('inventory')->paginate(10); // 10 items per page
        $items_count = Item::all();
        $totalItems = $items_count->count(); // get total number of items

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
        ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
