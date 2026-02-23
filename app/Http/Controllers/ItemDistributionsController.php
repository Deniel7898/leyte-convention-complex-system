<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use App\Models\ItemDistribution;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'type' => 'required|in:0,1',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'integer',
            'status' => 'required|string|max:255', // use the form value
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->inventory_ids as $inventoryId) {

                // Check if it's a consumable
                $consumable = InventoryConsumable::find($inventoryId);
                if ($consumable) {
                    ItemDistribution::create([
                        'type' => $request->type,
                        'description' => $request->description,
                        'remarks' => $request->remarks,
                        'quantity' => 1,
                        'distribution_date' => $request->distribution_date ?? now(),
                        'due_date' => $request->due_date,
                        'status' => $request->status,                 // <-- from form
                        'inventory_consumable_id' => $consumable->id,
                        'created_by' => auth()->id(),                // <-- auth helper
                        'updated_by' => auth()->id(),
                    ]);
                    continue; // skip to next inventory ID
                }

                // Otherwise, non-consumable
                $nonConsumable = InventoryNonConsumable::find($inventoryId);
                if ($nonConsumable) {
                    ItemDistribution::create([
                        'type' => $request->type,
                        'description' => $request->description,
                        'remarks' => $request->remarks,
                        'quantity' => 1,
                        'distribution_date' => $request->distribution_date ?? now(),
                        'due_date' => $request->due_date,
                        'status' => $request->status,                // <-- from form
                        'inventory_non_consumable_id' => $nonConsumable->id,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }
        });

        // Get all distributions with inventory -> item
        $itemDistributions = ItemDistribution::with([
            'inventory_consumable.item',
            'inventory_non_consumable.item',
        ])->latest()->get();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'New Distirbution added successfully',
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
