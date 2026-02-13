<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Units;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use Illuminate\Support\Facades\Auth;

class InventoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Get all consumables and non-consumables with relationships
        $consumables = InventoryConsumable::with(['item', 'qr_code'])->get();
        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])->get();

        // Merge both collections
        $inventories = $consumables->merge($nonConsumables)
            ->sortByDesc('receive_date') // Optional sorting
            ->values(); // Reset keys

        $inventories_table = view('inventory.inventory.table', compact('inventories'))->render();

        // Return main view with the table
        return view('inventory.inventory.index', compact('inventories_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.inventory.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle picture upload
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['availability'] = 1;
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // Save the new item and get it in a variable
        $item = Item::create($validated);

        // Loop only for the quantity of the newly added item
        for ($i = 0; $i < $item->quantity; $i++) {
            if ((int) $item->type === 0) {
                InventoryConsumable::create([
                    'item_id' => $item->id,
                    'receive_date' => now(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } else {
                InventoryNonConsumable::create([
                    'item_id' => $item->id,
                    'warranty_expires' => $request->warranty_expires,
                    'receive_date' => now(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        $consumables = InventoryConsumable::with(['item', 'qr_code'])->get();
        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])->get();

        // Merge and sort
        $inventories = $consumables->merge($nonConsumables)
            ->sortByDesc('receive_date')
            ->values();

        // Return the table partial with inventories
        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Item added successfully',
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
    public function edit($id)
    {
        $categories = Category::all();
        $units = Units::all();

        // Try to find in consumables first
        $inventory = InventoryConsumable::with('item')->find($id);

        // If not found, try non-consumables
        if (!$inventory) {
            $inventory = InventoryNonConsumable::with('item')->findOrFail($id);
        }

        return view('inventory.inventory.form', compact('inventory', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the input
        $validated = $request->validate([
            'warranty_expires' => 'nullable|date',
        ]);

        $item = Item::findOrFail($id);

        // Only update non-consumables
        if ($item->type == 1) {
            $item->update([
                'warranty_expires' => $validated['warranty_expires'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Also update all related InventoryNonConsumable records
            InventoryNonConsumable::where('item_id', $item->id)
                ->update(['warranty_expires' => $validated['warranty_expires'] ?? null]);
        }

        // Refresh inventories table for response
        $consumables = InventoryConsumable::with(['item', 'qr_code'])->get()->each(function ($c) {
            $c->type = 'Consumable';
            $c->description = $c->description ?? '--';
            $c->warranty_expires = $c->warranty_expires ?? '--';
        });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])->get()->each(function ($n) {
            $n->type = 'Non-Consumable';
            $n->description = $n->description ?? '--';
            $n->warranty_expires = $n->warranty_expires ?? '--';
        });

        $inventories = $consumables->merge($nonConsumables)
            ->sortByDesc('receive_date')
            ->values();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Warranty updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Try to find in consumables first
        $inventoryConsumable = InventoryConsumable::find($id);
        $inventoryNonConsumable = InventoryNonConsumable::find($id);

        if ($inventoryConsumable) {
            $inventoryConsumable->delete();
        } elseif ($inventoryNonConsumable) {
            $inventoryNonConsumable->delete();
        } else {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $consumables = InventoryConsumable::with(['item', 'qr_code'])->get()->each(function ($c) {
            $c->type = 'Consumable';
            $c->description = $c->description ?? '--';
            $c->warranty_expires = $c->warranty_expires ?? '--';
        });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])->get()->each(function ($n) {
            $n->type = 'Non-Consumable';
            $n->description = $n->description ?? '--';
            $n->warranty_expires = $n->warranty_expires ?? '--';
        });

        $inventories = $consumables->merge($nonConsumables)
            ->sortByDesc('receive_date')
            ->values();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
