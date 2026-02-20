<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use App\Models\ItemDistribution;

class ItemDistributionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemDistributions = ItemDistribution::all();
        $itemDistributions_table = view('item_distributions.table', compact('itemDistributions'))->render();
        return view('item_distributions.index', compact('itemDistributions_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $items = Item::with(['unit', 'inventoryConsumables', 'inventoryNonConsumables'])->get();

        // Optionally pre-select an item if passed via query
        $selectedItem = null;
        if ($request->has('item_id')) {
            $selectedItem = $items->find($request->item_id);
        }

        // Default to first item if no selection
        $selectedItem = $selectedItem ?? $items->first();

        return view('item_distributions.form', compact('items', 'selectedItem'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
